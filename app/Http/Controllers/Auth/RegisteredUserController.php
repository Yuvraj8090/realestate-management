<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'roles' => UserRole::registrationRoles(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'role' => $request->input('role', UserRole::PropertyOwner->value),
        ]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(UserRole::values())],
            'company_name' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->input('role') === UserRole::Company->value)],
            'company_registration_number' => ['nullable', 'string', 'max:100'],
            'company_license_number' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        if ($user->isRole(UserRole::Company)) {
            $companyName = (string) $request->string('company_name');

            Company::create([
                'user_id' => $user->id,
                'name' => $companyName,
                'slug' => $this->generateUniqueSlug($companyName),
                'registration_number' => $request->company_registration_number,
                'license_number' => $request->company_license_number,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    private function generateUniqueSlug(string $companyName): string
    {
        $baseSlug = Str::slug($companyName);
        $slug = $baseSlug;
        $counter = 2;

        while (Company::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
