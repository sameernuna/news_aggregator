<?php

namespace App\Http\Controllers\V1\Category;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class NewsCategoryController extends Controller
{
    /**
     * Display a listing of the news categories.
     */
    public function index(): JsonResponse
    {
        $categories = NewsCategory::withCount('articles')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'News categories retrieved successfully',
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created news category.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:news_categories',
            'description' => 'nullable|string|max:500',
        ]);

        $category = NewsCategory::create($request->only(['name', 'description']));

        return response()->json([
            'status' => 'success',
            'message' => 'News category created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified news category.
     */
    public function show(NewsCategory $newsCategory): JsonResponse
    {
        $newsCategory->load(['articles' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return response()->json([
            'status' => 'success',
            'message' => 'News category retrieved successfully',
            'data' => $newsCategory
        ]);
    }

    /**
     * Update the specified news category.
     */
    public function update(Request $request, NewsCategory $newsCategory): JsonResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('news_categories')->ignore($newsCategory->id)
            ],
            'description' => 'nullable|string|max:500',
        ]);

        $newsCategory->update($request->only(['name', 'description']));

        return response()->json([
            'status' => 'success',
            'message' => 'News category updated successfully',
            'data' => $newsCategory
        ]);
    }

    /**
     * Remove the specified news category.
     */
    public function destroy(NewsCategory $newsCategory): JsonResponse
    {
        // Check if category has articles
        if ($newsCategory->articles()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete category that has articles. Please remove or reassign articles first.'
            ], 422);
        }

        $newsCategory->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'News category deleted successfully'
        ]);
    }

    /**
     * Get articles by category.
     */
    public function articles(NewsCategory $newsCategory): JsonResponse
    {
        $articles = $newsCategory->articles()
            ->with(['author', 'publisher'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'message' => 'Articles for category retrieved successfully',
            'data' => [
                'category' => $newsCategory,
                'articles' => $articles
            ]
        ]);
    }
} 