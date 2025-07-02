<?php

namespace App\Http\Controllers\V1\Publisher;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PublisherController extends Controller
{
    /**
     * Display a listing of the publishers.
     */
    public function index(): JsonResponse
    {
        try {
            $publishers = Publisher::withCount('articles')->get();
            return response()->json([
                'status' => 'success',
                'message' => 'Publishers retrieved successfully',
                'data' => $publishers
            ]);
        } catch (\Exception $e) {
            Log::error('PublisherController@index failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve publishers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created publisher.
     */
    public function store(Request $request): JsonResponse
    {
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('PublisherController@store validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('PublisherController@store failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create publisher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified publisher.
     */
    public function show(Publisher $publisher): JsonResponse
    {
        try {
            $publisher->load(['articles' => function ($query) {
                $query->latest()->limit(10);
            }]);
            return response()->json([
                'status' => 'success',
                'message' => 'Publisher retrieved successfully',
                'data' => $publisher
            ]);
        } catch (\Exception $e) {
            Log::error('PublisherController@show failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve publisher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified publisher.
     */
    public function update(Request $request, Publisher $publisher): JsonResponse
    {
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('PublisherController@update validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('PublisherController@update failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update publisher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified publisher.
     */
    public function destroy(Publisher $publisher): JsonResponse
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('PublisherController@destroy failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete publisher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get articles by publisher.
     */
    public function articles(Publisher $publisher): JsonResponse
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('PublisherController@articles failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve articles for publisher: ' . $e->getMessage()
            ], 500);
        }
    }
} 