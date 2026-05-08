<?php

namespace App\Http\Controllers;

use App\Enums\PropertyStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Property;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $totalProperties = match ($user->role) {
            UserRole::SuperAdmin => Property::count(),
            UserRole::Company => $user->company?->properties()->count() ?? 0,
            default => $user->properties()->count(),
        };

        $publishedProperties = match ($user->role) {
            UserRole::SuperAdmin => Property::where('status', PropertyStatus::Published->value)->count(),
            UserRole::Company => $user->company?->properties()->where('status', PropertyStatus::Published->value)->count() ?? 0,
            default => $user->properties()->where('status', PropertyStatus::Published->value)->count(),
        };

        $stats = match ($user->role) {
            UserRole::SuperAdmin => [
                ['label' => 'Registered users', 'value' => User::count()],
                ['label' => 'Verified companies', 'value' => Company::where('verification_status', 'verified')->count()],
                ['label' => 'Live properties', 'value' => $publishedProperties],
                ['label' => 'Listings in draft', 'value' => Property::where('status', PropertyStatus::Draft->value)->count()],
            ],
            UserRole::Company => [
                ['label' => 'Company listings', 'value' => $totalProperties],
                ['label' => 'Published listings', 'value' => $publishedProperties],
                ['label' => 'Draft listings', 'value' => $user->company?->properties()->where('status', PropertyStatus::Draft->value)->count() ?? 0],
                ['label' => 'Verification status', 'value' => str($user->company?->verification_status?->value ?? 'pending')->headline()],
            ],
            UserRole::Broker => [
                ['label' => 'Broker-managed listings', 'value' => $totalProperties],
                ['label' => 'Published listings', 'value' => $publishedProperties],
                ['label' => 'Draft listings', 'value' => $user->properties()->where('status', PropertyStatus::Draft->value)->count()],
                ['label' => 'Client module', 'value' => 'Coming soon'],
            ],
            UserRole::PropertyOwner => [
                ['label' => 'Owner listings', 'value' => $totalProperties],
                ['label' => 'Published listings', 'value' => $publishedProperties],
                ['label' => 'Draft listings', 'value' => $user->properties()->where('status', PropertyStatus::Draft->value)->count()],
                ['label' => 'Inquiry inbox', 'value' => 'Coming soon'],
            ],
        };

        $roadmap = match ($user->role) {
            UserRole::SuperAdmin => [
                'Moderate newly created companies and approve verification requests.',
                'Monitor fresh property submissions from owners, companies, and brokers.',
                'Prepare monetization rules for featured listings and subscription plans.',
            ],
            UserRole::Company => [
                'Add your first sales, rental, and short-stay listings from the company panel.',
                'Upload office verification data so the admin can approve your firm.',
                'Assign future team members and broker relationships under the same firm.',
            ],
            UserRole::Broker => [
                'Create broker-managed listings and connect them to client owners.',
                'Track inbound inquiries and convert them into organized leads.',
                'Build a reusable buyer and tenant pipeline inside the broker workspace.',
            ],
            UserRole::PropertyOwner => [
                'Publish direct owner listings for sale, rent, or short stays.',
                'Manage photo galleries, pricing, and property availability from one place.',
                'Respond to buyer and tenant inquiries without needing a broker account.',
            ],
        };

        return view('dashboard', [
            'user' => $user,
            'stats' => $stats,
            'roadmap' => $roadmap,
            'recentProperties' => Property::query()
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }

    public function admin(): RedirectResponse
    {
        return redirect()->route('admin.properties.index');
    }

    public function company(): RedirectResponse
    {
        return redirect()->route('properties.manage');
    }

    public function owner(): RedirectResponse
    {
        return redirect()->route('properties.manage');
    }

    public function broker(): RedirectResponse
    {
        return redirect()->route('leads.index');
    }
}
