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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class UserPreferenceController extends Controller
{
    /**
     * Display a listing of the user's preferences.
     */
    public function index(Request $request): JsonResponse
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('UserPreferenceController@index failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve preferences: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created user preference.
     */
    public function store(Request $request): JsonResponse
    {
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('UserPreferenceController@store validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('UserPreferenceController@store failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create preference: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user preference.
     */
    public function show(UserPreference $userPreference, Request $request): JsonResponse
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('UserPreferenceController@show failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve preference: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified user preference.
     */
    public function update(Request $request, UserPreference $userPreference): JsonResponse
    {
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('UserPreferenceController@update validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('UserPreferenceController@update failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update preference: ' . $e->getMessage()
            ], 500);
        }
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
     * Get user's preferred articles from the user_preferred_news table.
     */
    public function getPreferredArticles(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Get query parameters for filtering
            $isRead = $request->query('is_read');
            $limit = $request->query('limit', 15);
            $days = $request->query('days'); // Optional: filter by recent days
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $authorId = $request->query('author_id');
            $categoryId = $request->query('category_id');
            $publisherId = $request->query('publisher_id');
            $search = $request->query('search');
            
            // Start with user's preferred news
            $query = $user->preferredNews()
                ->with(['article.author', 'article.publisher', 'article.newsCategory', 'article.keywords']);
            
            // Apply basic filters
            if ($isRead !== null) {
                $query->where('is_read', filter_var($isRead, FILTER_VALIDATE_BOOLEAN));
            }
            
            if ($days) {
                $query->recent($days);
            }
            
            // Apply date range filters
            if ($startDate) {
                $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
            }
            
            if ($endDate) {
                $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
            }
            
            // Get paginated results
            $preferredNews = $query->latest()->paginate($limit);
            
            // Transform the data to include article details and apply article-level filters
            $articles = $preferredNews->getCollection()->map(function ($preferredNews) {
                $article = $preferredNews->article;
                if ($article) {
                    $article->is_read = $preferredNews->is_read;
                    $article->preferred_at = $preferredNews->created_at;
                    return $article;
                }
                return null;
            })->filter();
            
            // Apply article-level filters
            if ($authorId) {
                $articles = $articles->filter(function ($article) use ($authorId) {
                    return $article->author_id == $authorId;
                });
            }
            
            if ($categoryId) {
                $articles = $articles->filter(function ($article) use ($categoryId) {
                    return $article->category_id == $categoryId;
                });
            }
            
            if ($publisherId) {
                $articles = $articles->filter(function ($article) use ($publisherId) {
                    return $article->source_id == $publisherId;
                });
            }
            
            if ($search) {
                $searchLower = strtolower($search);
                $articles = $articles->filter(function ($article) use ($searchLower) {
                    return str_contains(strtolower($article->title), $searchLower) ||
                           str_contains(strtolower($article->content), $searchLower);
                });
            }
            
            // Recalculate pagination after filtering
            $total = $articles->count();
            $perPage = $limit;
            $currentPage = $request->query('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $paginatedArticles = $articles->slice($offset, $perPage);
            
            // Create custom pagination response
            $response = [
                'current_page' => (int) $currentPage,
                'data' => $paginatedArticles->values(),
                'first_page_url' => $request->url() . '?' . http_build_query(array_merge($request->query(), ['page' => 1])),
                'from' => $offset + 1,
                'last_page' => (int) ceil($total / $perPage),
                'last_page_url' => $request->url() . '?' . http_build_query(array_merge($request->query(), ['page' => ceil($total / $perPage)])),
                'next_page_url' => $currentPage < ceil($total / $perPage) ? 
                    $request->url() . '?' . http_build_query(array_merge($request->query(), ['page' => $currentPage + 1])) : null,
                'path' => $request->url(),
                'per_page' => $perPage,
                'prev_page_url' => $currentPage > 1 ? 
                    $request->url() . '?' . http_build_query(array_merge($request->query(), ['page' => $currentPage - 1])) : null,
                'to' => min($offset + $perPage, $total),
                'total' => $total,
            ];
            
            return response()->json([
                'status' => 'success',
                'message' => 'Preferred articles retrieved successfully',
                'data' => $response
            ]);
            
        } catch (\Exception $e) {
            Log::error('UserPreferenceController@getPreferredArticles failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve preferred articles: ' . $e->getMessage()
            ], 500);
        }
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
     * Mark a preferred article as read or unread.
     */
    public function markAsRead(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'article_id' => 'required|integer|exists:news_articles,id',
                'is_read' => 'boolean'
            ]);

            $user = $request->user();
            $isRead = $request->input('is_read', true);

            $preferredNews = $user->preferredNews()
                ->where('article_id', $request->article_id)
                ->first();

            if (!$preferredNews) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Article not found in your preferred news'
                ], 404);
            }

            $preferredNews->update(['is_read' => $isRead]);

            return response()->json([
                'status' => 'success',
                'message' => $isRead ? 'Article marked as read' : 'Article marked as unread',
                'data' => $preferredNews
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('UserPreferenceController@markAsRead validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('UserPreferenceController@markAsRead failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update article status: ' . $e->getMessage()
            ], 500);
        }
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
