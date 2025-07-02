<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use App\Models\NewsCategory;
use App\Models\Publisher;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UserPreferenceController extends Controller
{
    /**
     * Display a listing of the user's preferences.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Get preferences grouped by type
        $categoryPreferences = $user->preferences()
            ->where('preference_type', 'category')
            ->with('category')
            ->get();

        $sourcePreferences = $user->preferences()
            ->where('preference_type', 'source')
            ->with('publisher')
            ->get();

        $authorPreferences = $user->preferences()
            ->where('preference_type', 'author')
            ->with('author')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'User preferences retrieved successfully',
            'data' => [
                'categories' => $categoryPreferences,
                'sources' => $sourcePreferences,
                'authors' => $authorPreferences,
            ]
        ]);
    }

    /**
     * Store a newly created user preference.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'preference_type' => ['required', 'string', Rule::in(['category', 'source', 'author'])],
            'preference_id' => 'required|integer',
        ]);

        $user = $request->user();

        // Validate that the preference_id exists in the respective table
        $this->validatePreferenceExists($request->preference_type, $request->preference_id);

        // Check if preference already exists
        $existingPreference = $user->preferences()
            ->where('preference_type', $request->preference_type)
            ->where('preference_id', $request->preference_id)
            ->first();

        if ($existingPreference) {
            return response()->json([
                'status' => 'error',
                'message' => 'Preference already exists'
            ], 422);
        }

        $preference = $user->preferences()->create([
            'preference_type' => $request->preference_type,
            'preference_id' => $request->preference_id,
        ]);

        // Load the appropriate relationship based on preference type
        switch ($preference->preference_type) {
            case 'category':
                $preference->load('category');
                break;
            case 'source':
                $preference->load('publisher');
                break;
            case 'author':
                $preference->load('author');
                break;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User preference created successfully',
            'data' => $preference
        ], 201);
    }

    /**
     * Display the specified user preference.
     */
    public function show(UserPreference $userPreference, Request $request): JsonResponse
    {
        $user = $request->user();

        // Ensure the preference belongs to the authenticated user
        if ($userPreference->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to preference'
            ], 403);
        }

        // Load the appropriate relationship based on preference type
        switch ($userPreference->preference_type) {
            case 'category':
                $userPreference->load('category');
                break;
            case 'source':
                $userPreference->load('publisher');
                break;
            case 'author':
                $userPreference->load('author');
                break;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User preference retrieved successfully',
            'data' => $userPreference
        ]);
    }

    /**
     * Update the specified user preference.
     */
    public function update(Request $request, UserPreference $userPreference): JsonResponse
    {
        $user = $request->user();

        // Ensure the preference belongs to the authenticated user
        if ($userPreference->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to preference'
            ], 403);
        }

        $request->validate([
            'preference_type' => ['required', 'string', Rule::in(['category', 'source', 'author'])],
            'preference_id' => 'required|integer',
        ]);

        // Validate that the preference_id exists in the respective table
        $this->validatePreferenceExists($request->preference_type, $request->preference_id);

        // Check if the new preference already exists (excluding current one)
        $existingPreference = $user->preferences()
            ->where('id', '!=', $userPreference->id)
            ->where('preference_type', $request->preference_type)
            ->where('preference_id', $request->preference_id)
            ->first();

        if ($existingPreference) {
            return response()->json([
                'status' => 'error',
                'message' => 'Preference already exists'
            ], 422);
        }

        $userPreference->update([
            'preference_type' => $request->preference_type,
            'preference_id' => $request->preference_id,
        ]);

        // Load the appropriate relationship based on preference type
        switch ($userPreference->preference_type) {
            case 'category':
                $userPreference->load('category');
                break;
            case 'source':
                $userPreference->load('publisher');
                break;
            case 'author':
                $userPreference->load('author');
                break;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User preference updated successfully',
            'data' => $userPreference
        ]);
    }

    /**
     * Remove the specified user preference.
     */
    public function destroy(UserPreference $userPreference, Request $request): JsonResponse
    {
        $user = $request->user();

        // Ensure the preference belongs to the authenticated user
        if ($userPreference->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to preference'
            ], 403);
        }

        $userPreference->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User preference deleted successfully'
        ]);
    }

    /**
     * Get user's preferred articles based on preferences.
     */
    public function getPreferredArticles(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = \App\Models\NewsArticle::with(['author', 'publisher', 'newsCategory', 'keywords']);

        // Get user preferences
        $categoryPreferences = $user->categoryPreferences()->pluck('preference_id');
        $sourcePreferences = $user->sourcePreferences()->pluck('preference_id');
        $authorPreferences = $user->authorPreferences()->pluck('preference_id');

        // Apply preference filters
        if ($categoryPreferences->isNotEmpty() || $sourcePreferences->isNotEmpty() || $authorPreferences->isNotEmpty()) {
            $query->where(function ($q) use ($categoryPreferences, $sourcePreferences, $authorPreferences) {
                if ($categoryPreferences->isNotEmpty()) {
                    $q->orWhereIn('category_id', $categoryPreferences);
                }
                if ($sourcePreferences->isNotEmpty()) {
                    $q->orWhereIn('source_id', $sourcePreferences);
                }
                if ($authorPreferences->isNotEmpty()) {
                    $q->orWhereIn('author_id', $authorPreferences);
                }
            });
        }

        $articles = $query->latest()->paginate(15);

        return response()->json([
            'status' => 'success',
            'message' => 'Preferred articles retrieved successfully',
            'data' => $articles
        ]);
    }

    /**
     * Bulk add preferences.
     */
    public function bulkStore(Request $request): JsonResponse
    {
        $request->validate([
            'preferences' => 'required|array',
            'preferences.*.preference_type' => ['required', 'string', Rule::in(['category', 'source', 'author'])],
            'preferences.*.preference_id' => 'required|integer',
        ]);

        $user = $request->user();
        $createdPreferences = [];
        $errors = [];

        foreach ($request->preferences as $index => $preferenceData) {
            try {
                // Validate that the preference_id exists
                $this->validatePreferenceExists($preferenceData['preference_type'], $preferenceData['preference_id']);

                // Check if preference already exists
                $existingPreference = $user->preferences()
                    ->where('preference_type', $preferenceData['preference_type'])
                    ->where('preference_id', $preferenceData['preference_id'])
                    ->first();

                if (!$existingPreference) {
                    $preference = $user->preferences()->create([
                        'preference_type' => $preferenceData['preference_type'],
                        'preference_id' => $preferenceData['preference_id'],
                    ]);

                    // Load the appropriate relationship based on preference type
                    switch ($preference->preference_type) {
                        case 'category':
                            $preference->load('category');
                            break;
                        case 'source':
                            $preference->load('publisher');
                            break;
                        case 'author':
                            $preference->load('author');
                            break;
                    }

                    $createdPreferences[] = $preference;
                }
            } catch (\Exception $e) {
                $errors[] = "Preference at index {$index}: " . $e->getMessage();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Bulk preferences processed',
            'data' => [
                'created' => $createdPreferences,
                'errors' => $errors
            ]
        ], 201);
    }

    /**
     * Clear all user preferences.
     */
    public function clearAll(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $user->preferences()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'All user preferences cleared successfully'
        ]);
    }

    /**
     * Validate that the preference exists in the respective table.
     */
    private function validatePreferenceExists(string $type, int $id): void
    {
        switch ($type) {
            case 'category':
                if (!NewsCategory::find($id)) {
                    throw new \InvalidArgumentException('Category not found');
                }
                break;
            case 'source':
                if (!Publisher::find($id)) {
                    throw new \InvalidArgumentException('Publisher not found');
                }
                break;
            case 'author':
                if (!Author::find($id)) {
                    throw new \InvalidArgumentException('Author not found');
                }
                break;
        }
    }
}
