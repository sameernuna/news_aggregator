<?php

namespace App\Http\Controllers\V1\Category;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class NewsCategoryController extends Controller
{
    /**
     * Display a listing of the news categories.
     */
    public function index(): JsonResponse
    {
        try {
            $categories = NewsCategory::withCount('articles')->get();
            return response()->json([
                'status' => 'success',
                'message' => 'News categories retrieved successfully',
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            Log::error('NewsCategoryController@index failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created news category.
     */
    public function store(Request $request): JsonResponse
    {
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('NewsCategoryController@store validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('NewsCategoryController@store failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified news category.
     */
    public function show(NewsCategory $newsCategory): JsonResponse
    {
        try {
            $newsCategory->load(['articles' => function ($query) {
                $query->latest()->limit(10);
            }]);
            return response()->json([
                'status' => 'success',
                'message' => 'News category retrieved successfully',
                'data' => $newsCategory
            ]);
        } catch (\Exception $e) {
            Log::error('NewsCategoryController@show failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified news category.
     */
    public function update(Request $request, NewsCategory $newsCategory): JsonResponse
    {
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('NewsCategoryController@update validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('NewsCategoryController@update failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified news category.
     */
    public function destroy(NewsCategory $newsCategory): JsonResponse
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('NewsCategoryController@destroy failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get articles by category.
     */
    public function articles(NewsCategory $newsCategory): JsonResponse
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('NewsCategoryController@articles failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve articles for category: ' . $e->getMessage()
            ], 500);
        }
    }
} 