<?php

namespace App\Http\Controllers\V1\Publisher;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class PublisherController extends Controller
{
    /**
     * Display a listing of the publishers.
     */
    public function index(): JsonResponse
    {
        $publishers = Publisher::withCount('articles')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Publishers retrieved successfully',
            'data' => $publishers
        ]);
    }

    /**
     * Store a newly created publisher.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:publishers',
            'description' => 'nullable|string|max:500',
        ]);

        $publisher = Publisher::create($request->only(['name', 'description']));

        return response()->json([
            'status' => 'success',
            'message' => 'Publisher created successfully',
            'data' => $publisher
        ], 201);
    }

    /**
     * Display the specified publisher.
     */
    public function show(Publisher $publisher): JsonResponse
    {
        $publisher->load(['articles' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return response()->json([
            'status' => 'success',
            'message' => 'Publisher retrieved successfully',
            'data' => $publisher
        ]);
    }

    /**
     * Update the specified publisher.
     */
    public function update(Request $request, Publisher $publisher): JsonResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('publishers')->ignore($publisher->id)
            ],
            'description' => 'nullable|string|max:500',
        ]);

        $publisher->update($request->only(['name', 'description']));

        return response()->json([
            'status' => 'success',
            'message' => 'Publisher updated successfully',
            'data' => $publisher
        ]);
    }

    /**
     * Remove the specified publisher.
     */
    public function destroy(Publisher $publisher): JsonResponse
    {
        // Check if publisher has articles
        if ($publisher->articles()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete publisher that has articles. Please remove or reassign articles first.'
            ], 422);
        }

        $publisher->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Publisher deleted successfully'
        ]);
    }

    /**
     * Get articles by publisher.
     */
    public function articles(Publisher $publisher): JsonResponse
    {
        $articles = $publisher->articles()
            ->with(['author', 'newsCategory'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'message' => 'Articles for publisher retrieved successfully',
            'data' => [
                'publisher' => $publisher,
                'articles' => $articles
            ]
        ]);
    }
} 