<?php

namespace App\Http\Controllers;

use App\Enums\PropertyStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Property;
use App\Models\User;
use Illuminate\Contracts\View\View;
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
            UserRole::SuperAdmin => Property::where('status', PropertyStatus::Published)->count(),
            UserRole::Company => $user->company?->properties()->where('status', PropertyStatus::Published)->count() ?? 0,
            default => $user->properties()->where('status', PropertyStatus::Published)->count(),
        };

        $stats = match ($user->role) {
            UserRole::SuperAdmin => [
                ['label' => 'Registered users', 'value' => User::count()],
                ['label' => 'Verified companies', 'value' => Company::where('verification_status', 'verified')->count()],
                ['label' => 'Live properties', 'value' => $publishedProperties],
                ['label' => 'Listings in draft', 'value' => Property::where('status', PropertyStatus::Draft)->count()],
            ],
            UserRole::Company => [
                ['label' => 'Company listings', 'value' => $totalProperties],
                ['label' => 'Published listings', 'value' => $publishedProperties],
                ['label' => 'Draft listings', 'value' => $user->company?->properties()->where('status', PropertyStatus::Draft)->count() ?? 0],
                ['label' => 'Verification status', 'value' => str($user->company?->verification_status?->value ?? 'pending')->headline()],
            ],
            UserRole::Broker => [
                ['label' => 'Broker-managed listings', 'value' => $totalProperties],
                ['label' => 'Published listings', 'value' => $publishedProperties],
                ['label' => 'Draft listings', 'value' => $user->properties()->where('status', PropertyStatus::Draft)->count()],
                ['label' => 'Client module', 'value' => 'Coming soon'],
            ],
            UserRole::PropertyOwner => [
                ['label' => 'Owner listings', 'value' => $totalProperties],
                ['label' => 'Published listings', 'value' => $publishedProperties],
                ['label' => 'Draft listings', 'value' => $user->properties()->where('status', PropertyStatus::Draft)->count()],
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

    public function admin(): View
    {
        return view('placeholders.coming-soon', [
            'title' => 'Admin User Management',
            'description' => 'This protected space will become the command center for moderation, approvals, and platform-wide controls.',
            'items' => [
                'Approve or reject company verification requests with audit notes.',
                'Suspend suspicious owners, brokers, or firms without deleting their data.',
                'Review flagged listings before they appear publicly.',
            ],
        ]);
    }

    public function company(): View
    {
        return view('placeholders.coming-soon', [
            'title' => 'Company Listing Workspace',
            'description' => 'This area is reserved for verified real estate firms and will expand into a full property operations panel.',
            'items' => [
                'Create listings under the company brand with unified media and pricing.',
                'Track draft, published, sold, and rented inventory.',
                'Invite staff members under the same company account structure.',
            ],
        ]);
    }

    public function owner(): View
    {
        return view('placeholders.coming-soon', [
            'title' => 'Owner Property Workspace',
            'description' => 'This owner-only area will support direct property management without relying on a brokerage firm.',
            'items' => [
                'Manage self-listed homes for rent, sale, and short stays.',
                'Control pricing, availability, and media updates in one dashboard.',
                'Review incoming leads and contact requests from prospects.',
            ],
        ]);
    }

    public function broker(): View
    {
        return view('placeholders.coming-soon', [
            'title' => 'Broker Lead Workspace',
            'description' => 'This broker-only area will grow into the lead and client management center for agents.',
            'items' => [
                'Manage broker-listed inventory across multiple property owners.',
                'Track inquiries, site visits, and negotiations with clients.',
                'Organize buyer and tenant pipelines around active properties.',
            ],
        ]);
    }
}
