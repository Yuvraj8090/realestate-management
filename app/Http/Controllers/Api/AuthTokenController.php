<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IssueApiTokenRequest;
use App\Http\Resources\UserSummaryResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthTokenController extends Controller
{
    public function store(IssueApiTokenRequest $request): JsonResponse
    {
        $user = User::query()
            ->where('email', $request->string('email')->toString())
            ->first();

        if ($user === null || ! Hash::check($request->string('password')->toString(), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['This account is currently inactive.'],
            ]);
        }

        $token = $user->createToken($request->string('device_name')->toString());

        return response()->json([
            'message' => 'API token issued successfully.',
            'token_type' => 'Bearer',
            'token' => $token->plainTextToken,
            'user' => new UserSummaryResource($user->loadMissing('company')),
        ], 201);
    }

    public function me(Request $request): UserSummaryResource
    {
        return new UserSummaryResource($request->user()->loadMissing('company'));
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Current API token revoked successfully.',
        ]);
    }
}
