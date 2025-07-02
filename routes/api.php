<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\LoginController;
use App\Http\Controllers\V1\Auth\LogoutController;
use App\Http\Controllers\V1\Auth\RegisterController;
use App\Http\Controllers\V1\Category\NewsCategoryController;
use App\Http\Controllers\V1\Publisher\PublisherController;
use App\Http\Controllers\V1\Author\AuthorController;
use App\Http\Controllers\V1\Article\NewsArticleController;
use App\Http\Controllers\V1\UserPreferenceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/test', function () {
    return response()->json(['message' => 'General test']);
});

// Test error handling routes
Route::get('/test-error', function () {
    throw new \Exception('Test error for debugging');
});

Route::get('/test-auth', function () {
    return response()->json(['message' => 'This should require auth']);
})->middleware('auth:sanctum');

Route::group(['middleware' => ['api'],'prefix' => 'v1'], function () {
    /*
    |--------------------------------------------------------------------------
    | GENERAL ROUTES
    |--------------------------------------------------------------------------
    */

    Route::group(['prefix' => 'auth'], function () {

        Route::post('login', [LoginController::class, 'login'])->name('login');
        Route::post('register', [RegisterController::class, 'register'])->name('register');
        Route::get('test', function () {
            return response()->json(['message' => 'General test']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | PROTECTED ROUTES (Require Authentication)
    |--------------------------------------------------------------------------
    */
    Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'auth'], function () {
        Route::post('logout', [LogoutController::class, 'logout'])->name('logout');
    });

    /*
    |--------------------------------------------------------------------------
    | NEWS CATEGORIES ROUTES
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'categories'], function () {
        // Public routes (no authentication required)
        Route::get('/', [NewsCategoryController::class, 'index'])->name('categories.index');
        Route::get('/{newsCategory}', [NewsCategoryController::class, 'show'])->name('categories.show');
        Route::get('/{newsCategory}/articles', [NewsCategoryController::class, 'articles'])->name('categories.articles');
        
        // Protected routes (require authentication)
        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::post('/', [NewsCategoryController::class, 'store'])->name('categories.store');
            Route::put('/{newsCategory}', [NewsCategoryController::class, 'update'])->name('categories.update');
            Route::delete('/{newsCategory}', [NewsCategoryController::class, 'destroy'])->name('categories.destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | PUBLISHERS ROUTES
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'publishers'], function () {
        // Public routes (no authentication required)
        Route::get('/', [PublisherController::class, 'index'])->name('publishers.index');
        Route::get('/{publisher}', [PublisherController::class, 'show'])->name('publishers.show');
        Route::get('/{publisher}/articles', [PublisherController::class, 'articles'])->name('publishers.articles');
        
        // Protected routes (require authentication)
        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::post('/', [PublisherController::class, 'store'])->name('publishers.store');
            Route::put('/{publisher}', [PublisherController::class, 'update'])->name('publishers.update');
            Route::delete('/{publisher}', [PublisherController::class, 'destroy'])->name('publishers.destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | AUTHORS ROUTES
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'authors'], function () {
        // Public routes (no authentication required)
        Route::get('/', [AuthorController::class, 'index'])->name('authors.index');
        Route::get('/{author}', [AuthorController::class, 'show'])->name('authors.show');
        Route::get('/{author}/articles', [AuthorController::class, 'articles'])->name('authors.articles');
        
        // Protected routes (require authentication) - No delete functionality
        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::post('/', [AuthorController::class, 'store'])->name('authors.store');
            Route::put('/{author}', [AuthorController::class, 'update'])->name('authors.update');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | NEWS ARTICLES ROUTES
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'articles'], function () {
        // Public routes (no authentication required)
        Route::get('/', [NewsArticleController::class, 'index'])->name('articles.index');
        Route::get('/{newsArticle}', [NewsArticleController::class, 'show'])->name('articles.show');
        Route::get('/slug/{slug}', [NewsArticleController::class, 'showBySlug'])->name('articles.showBySlug');
        Route::get('/{newsArticle}/related', [NewsArticleController::class, 'related'])->name('articles.related');
        
        // Protected routes (require authentication)
        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::post('/', [NewsArticleController::class, 'store'])->name('articles.store');
            Route::put('/{newsArticle}', [NewsArticleController::class, 'update'])->name('articles.update');
            Route::delete('/{newsArticle}', [NewsArticleController::class, 'destroy'])->name('articles.destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | USER PREFERENCES ROUTES (Require Authentication)
    |--------------------------------------------------------------------------
    */
    Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'preferences'], function () {
        Route::get('/', [UserPreferenceController::class, 'index'])->name('preferences.index');
        Route::post('/', [UserPreferenceController::class, 'store'])->name('preferences.store');
        Route::post('/bulk', [UserPreferenceController::class, 'bulkStore'])->name('preferences.bulkStore');
        Route::get('/articles', [UserPreferenceController::class, 'getPreferredArticles'])->name('preferences.articles');
        Route::delete('/clear', [UserPreferenceController::class, 'clearAll'])->name('preferences.clearAll');
        Route::get('/{userPreference}', [UserPreferenceController::class, 'show'])->name('preferences.show');
        Route::put('/{userPreference}', [UserPreferenceController::class, 'update'])->name('preferences.update');
        Route::delete('/{userPreference}', [UserPreferenceController::class, 'destroy'])->name('preferences.destroy');
    });

});