<?php

namespace App\Http\Controllers\V1\Article;

use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\NewsCategory;
use App\Models\Keyword;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class NewsArticleController extends Controller
{
    /**
     * Display a listing of the news articles.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = NewsArticle::with(['author', 'publisher', 'newsCategory', 'keywords']);

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by publisher
            if ($request->has('publisher_id')) {
                $query->where('source_id', $request->publisher_id);
            }

            // Filter by author
            if ($request->has('author_id')) {
                $query->where('author_id', $request->author_id);
            }

            // Filter by keyword
            if ($request->has('keyword')) {
                $query->whereHas('keywords', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->keyword . '%');
                });
            }

            // Search by title or content
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }

            $articles = $query->latest()->paginate(15);

            return response()->json([
                'status' => 'success',
                'message' => 'News articles retrieved successfully',
                'data' => $articles
            ]);
        } catch (\Exception $e) {
            Log::error('NewsArticleController@index failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve articles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created news article.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category_id' => 'required|exists:news_categories,id',
                'source_id' => 'required|exists:publishers,id',
                'author_id' => 'required|exists:authors,id',
                'published_at' => 'nullable|date',
                'keywords' => 'nullable|array',
                'keywords.*' => 'string|max:100',
            ]);

            // Generate slug from title
            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $counter = 1;

            // Ensure slug is unique
            while (NewsArticle::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $article = NewsArticle::create([
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'category_id' => $request->category_id,
                'source_id' => $request->source_id,
                'author_id' => $request->author_id,
                'published_at' => $request->published_at ?? now(),
            ]);

            // Handle keywords
            if ($request->has('keywords') && is_array($request->keywords)) {
                $keywordIds = [];
                foreach ($request->keywords as $keywordName) {
                    $keyword = Keyword::firstOrCreate(['name' => $keywordName]);
                    $keywordIds[] = $keyword->id;
                }
                $article->keywords()->attach($keywordIds);
            }

            $article->load(['author', 'publisher', 'newsCategory', 'keywords']);

            return response()->json([
                'status' => 'success',
                'message' => 'News article created successfully',
                'data' => $article
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('NewsArticleController@store validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('NewsArticleController@store failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create article: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified news article.
     */
    public function show(NewsArticle $newsArticle): JsonResponse
    {
        try {
            $newsArticle->load(['author', 'publisher', 'newsCategory', 'keywords']);

            return response()->json([
                'status' => 'success',
                'message' => 'News article retrieved successfully',
                'data' => $newsArticle
            ]);
        } catch (\Exception $e) {
            Log::error('NewsArticleController@show failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve article: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified news article.
     */
    public function update(Request $request, NewsArticle $newsArticle): JsonResponse
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category_id' => 'required|exists:news_categories,id',
                'source_id' => 'required|exists:publishers,id',
                'author_id' => 'required|exists:authors,id',
                'published_at' => 'nullable|date',
                'keywords' => 'nullable|array',
                'keywords.*' => 'string|max:100',
            ]);

            // Generate new slug if title changed
            if ($request->title !== $newsArticle->title) {
                $slug = Str::slug($request->title);
                $originalSlug = $slug;
                $counter = 1;

                while (NewsArticle::where('slug', $slug)->where('id', '!=', $newsArticle->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                $newsArticle->slug = $slug;
            }

            $newsArticle->update([
                'title' => $request->title,
                'content' => $request->content,
                'category_id' => $request->category_id,
                'source_id' => $request->source_id,
                'author_id' => $request->author_id,
                'published_at' => $request->published_at ?? $newsArticle->published_at,
            ]);

            // Handle keywords
            if ($request->has('keywords') && is_array($request->keywords)) {
                $keywordIds = [];
                foreach ($request->keywords as $keywordName) {
                    $keyword = Keyword::firstOrCreate(['name' => $keywordName]);
                    $keywordIds[] = $keyword->id;
                }
                $newsArticle->keywords()->sync($keywordIds);
            }

            $newsArticle->load(['author', 'publisher', 'newsCategory', 'keywords']);

            return response()->json([
                'status' => 'success',
                'message' => 'News article updated successfully',
                'data' => $newsArticle
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('NewsArticleController@update validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('NewsArticleController@update failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update article: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified news article.
     */
    public function destroy(NewsArticle $newsArticle): JsonResponse
    {
        try {
            $newsArticle->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'News article deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('NewsArticleController@destroy failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete article: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get article by slug.
     */
    public function showBySlug(string $slug): JsonResponse
    {
        try {
            $article = NewsArticle::where('slug', $slug)
                ->with(['author', 'publisher', 'newsCategory', 'keywords'])
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'message' => 'News article retrieved successfully',
                'data' => $article
            ]);
        } catch (\Exception $e) {
            Log::error('NewsArticleController@showBySlug failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve article by slug: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get related articles.
     */
    public function related(NewsArticle $newsArticle): JsonResponse
    {
        try {
            $relatedArticles = NewsArticle::where('id', '!=', $newsArticle->id)
                ->where(function ($query) use ($newsArticle) {
                    $query->where('category_id', $newsArticle->category_id)
                          ->orWhere('source_id', $newsArticle->source_id)
                          ->orWhere('author_id', $newsArticle->author_id)
                          ->orWhereHas('keywords', function ($q) use ($newsArticle) {
                              $q->whereIn('keywords.id', $newsArticle->keywords->pluck('id'));
                          });
                })
                ->with(['author', 'publisher', 'newsCategory', 'keywords'])
                ->latest()
                ->limit(5)
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Related articles retrieved successfully',
                'data' => $relatedArticles
            ]);
        } catch (\Exception $e) {
            Log::error('NewsArticleController@related failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve related articles: ' . $e->getMessage()
            ], 500);
        }
    }
} 