<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PropertyModerationStatus;
use App\Enums\PropertyStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkModerationRequest;
use App\Models\Property;
use App\Models\PropertyReport;
use App\Notifications\PropertyModerationStatusChangedNotification;
use App\Services\AdminActionLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PropertyModerationController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $status = $request->input('moderation_status', PropertyModerationStatus::Pending->value);

        $properties = Property::query()
            ->with(['images', 'user', 'company', 'reviewer'])
            ->withCount('reports')
            ->when($status, fn ($query) => $query->where('moderation_status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $logs = $request->user()->adminActionLogs()->latest()->limit(20)->get();
        $openReports = PropertyReport::query()->with('property')->latest()->limit(10)->get();

        if ($request->expectsJson()) {
            return response()->json([
                'properties' => $properties,
                'logs' => $logs,
                'reports' => $openReports,
            ]);
        }

        return view('admin.properties.index', [
            'properties' => $properties,
            'logs' => $logs,
            'openReports' => $openReports,
            'selectedStatus' => $status,
        ]);
    }

    public function update(Request $request, Property $property, AdminActionLogger $adminActionLogger): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'moderation_status' => ['required', Rule::in(PropertyModerationStatus::values())],
            'moderation_notes' => ['nullable', 'string', 'max:1000'],
            'rejection_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $this->applyModeration($property, $validated, $request->user(), $adminActionLogger);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Property moderation updated.',
                'property' => $property->fresh(['reviewer']),
            ]);
        }

        return back()->with('status', 'Property moderation updated.');
    }

    public function bulkUpdate(BulkModerationRequest $request, AdminActionLogger $adminActionLogger): RedirectResponse|JsonResponse
    {
        $properties = Property::query()
            ->whereIn('id', $request->validated('property_ids'))
            ->get();

        foreach ($properties as $property) {
            $this->applyModeration($property, $request->validated(), $request->user(), $adminActionLogger);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Bulk moderation update completed.']);
        }

        return back()->with('status', 'Bulk moderation update completed.');
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function applyModeration(Property $property, array $validated, $admin, AdminActionLogger $adminActionLogger): void
    {
        $property->update([
            'moderation_status' => $validated['moderation_status'],
            'moderation_notes' => $validated['moderation_notes'] ?? null,
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'status' => $validated['moderation_status'] === PropertyModerationStatus::Approved->value
                ? ($property->status === PropertyStatus::Draft ? PropertyStatus::Published->value : $property->status->value)
                : $property->status->value,
            'published_at' => $validated['moderation_status'] === PropertyModerationStatus::Approved->value
                ? ($property->published_at ?? now())
                : $property->published_at,
        ]);

        $property->user->notify(new PropertyModerationStatusChangedNotification($property->fresh()));

        $adminActionLogger->log($admin, 'property.moderated', $property, meta: [
            'moderation_status' => $property->moderation_status->value,
            'rejection_reason' => $property->rejection_reason,
        ]);
    }
}
