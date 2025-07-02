<?php

namespace App\Http\Controllers\V1\Author;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AuthorController extends Controller
{
    /**
     * Display a listing of the authors.
     */
    public function index(): JsonResponse
    {
        $authors = Author::withCount('articles')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Authors retrieved successfully',
            'data' => $authors
        ]);
    }

    /**
     * Store a newly created author.
     */
    public function store(Request $request): JsonResponse
    {
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
    }

    /**
     * Display the specified author.
     */
    public function show(Author $author): JsonResponse
    {
        $author->load(['articles' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return response()->json([
            'status' => 'success',
            'message' => 'Author retrieved successfully',
            'data' => $author
        ]);
    }

    /**
     * Update the specified author.
     */
    public function update(Request $request, Author $author): JsonResponse
    {
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
    }

    /**
     * Get articles by author.
     */
    public function articles(Author $author): JsonResponse
    {
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
    }
} 