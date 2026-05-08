<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use App\Services\AdminActionLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();
        $query = Lead::query()
            ->with(['property.images', 'inquiry', 'broker', 'assignee', 'notes.user']);

        if ($user->isRole(UserRole::SuperAdmin)) {
            //
        } elseif ($user->isRole(UserRole::Broker)) {
            $query->where(function ($query) use ($user): void {
                $query->where('broker_user_id', $user->id)
                    ->orWhere('assigned_to_user_id', $user->id);
            });
        } else {
            $query->where('assigned_to_user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $leads = $query->latest()->paginate(12)->withQueryString();

        if ($request->expectsJson()) {
            return LeadResource::collection($leads);
        }

        return view('leads.index', [
            'leads' => $leads,
        ]);
    }

    public function update(UpdateLeadRequest $request, Lead $lead, AdminActionLogger $adminActionLogger): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $isAllowed = $user->isRole(UserRole::SuperAdmin)
            || $lead->broker_user_id === $user->id
            || $lead->assigned_to_user_id === $user->id;

        abort_unless($isAllowed, 403);

        $lead->update([
            'status' => $request->input('status'),
            'assigned_to_user_id' => $user->isRole(UserRole::SuperAdmin) ? $request->input('assigned_to_user_id') : $lead->assigned_to_user_id,
            'booking_reference' => $request->input('booking_reference'),
            'is_converted' => $request->boolean('is_converted'),
            'converted_at' => $request->boolean('is_converted') ? now() : null,
            'last_contacted_at' => $request->input('status') === 'contacted' ? now() : $lead->last_contacted_at,
        ]);

        if ($request->filled('note')) {
            $lead->notes()->create([
                'user_id' => $user->id,
                'note' => $request->input('note'),
            ]);
        }

        if ($user->isRole(UserRole::SuperAdmin)) {
            $adminActionLogger->log($user, 'lead.updated', $lead, meta: [
                'status' => $lead->status->value,
                'assigned_to_user_id' => $lead->assigned_to_user_id,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Lead updated successfully.',
                'lead' => new LeadResource($lead->load(['property.images', 'inquiry.property', 'broker', 'assignee', 'notes.user'])),
            ]);
        }

        return back()->with('status', 'Lead updated successfully.');
    }
}
