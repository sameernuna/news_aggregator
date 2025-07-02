<?php

namespace App\Http\Controllers\V1\Author;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthorController extends Controller
{
    /**
     * Display a listing of the authors.
     */
    public function index(): JsonResponse
    {
        try {
            $authors = Author::withCount('articles')->get();
            return response()->json([
                'status' => 'success',
                'message' => 'Authors retrieved successfully',
                'data' => $authors
            ]);
        } catch (\Exception $e) {
            Log::error('AuthorController@index failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve authors: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created author.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|unique:authors',
                'profile_image_url' => 'nullable|url|max:255',
            ]);
            $author = Author::create($request->only(['name', 'email', 'profile_image_url']));
            return response()->json([
                'status' => 'success',
                'message' => 'Author created successfully',
                'data' => $author
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('AuthorController@store validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('AuthorController@store failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create author: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified author.
     */
    public function show(Author $author): JsonResponse
    {
        try {
            $author->load(['articles' => function ($query) {
                $query->latest()->limit(10);
            }]);
            return response()->json([
                'status' => 'success',
                'message' => 'Author retrieved successfully',
                'data' => $author
            ]);
        } catch (\Exception $e) {
            Log::error('AuthorController@show failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve author: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified author.
     */
    public function update(Request $request, Author $author): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('authors')->ignore($author->id)
                ],
                'profile_image_url' => 'nullable|url|max:255',
            ]);
            $author->update($request->only(['name', 'email', 'profile_image_url']));
            return response()->json([
                'status' => 'success',
                'message' => 'Author updated successfully',
                'data' => $author
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('AuthorController@update validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('AuthorController@update failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update author: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get articles by author.
     */
    public function articles(Author $author): JsonResponse
    {
        try {
            $articles = $author->articles()
                ->with(['publisher', 'newsCategory'])
                ->latest()
                ->paginate(15);
            return response()->json([
                'status' => 'success',
                'message' => 'Articles for author retrieved successfully',
                'data' => [
                    'author' => $author,
                    'articles' => $articles
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('AuthorController@articles failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve articles for author: ' . $e->getMessage()
            ], 500);
        }
    }
} 