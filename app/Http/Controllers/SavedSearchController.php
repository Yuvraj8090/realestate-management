<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveSearchRequest;
use App\Http\Resources\SavedSearchResource;
use App\Models\SavedSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SavedSearchController extends Controller
{
    public function store(SaveSearchRequest $request): RedirectResponse|JsonResponse
    {
        $savedSearch = $request->user()->savedSearches()->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json(new SavedSearchResource($savedSearch), 201);
        }

        return back()->with('status', 'Search saved successfully.');
    }

    public function destroy(Request $request, SavedSearch $savedSearch): RedirectResponse|JsonResponse
    {
        abort_unless($savedSearch->user_id === $request->user()->id, 403);
        $savedSearch->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Saved search deleted.']);
        }

        return back()->with('status', 'Saved search deleted.');
    }
}
