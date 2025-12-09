<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FoundItemController;
use App\Http\Controllers\Api\LostItemController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReturnController;
use Illuminate\Support\Facades\Route;

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

// Wrap ALL API routes with force.json to ensure JSON negotiation
Route::middleware('force.json')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    // Helper GET endpoint to nudge clients to use POST
    Route::get('/login', function () {
        return response()->json([
            'message' => 'Use POST /api/login with email and password to authenticate.'
        ], 405);
    });
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    
    // Public success stories endpoint (no auth required)
    Route::get('/success-stories', [ReturnController::class, 'getSuccessStories']);

// Protected routes (require authentication)
    Route::middleware(['auth:sanctum'])->name('api.')->group(function () {
    // Auth
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/stats/my', [DashboardController::class, 'myStats']);

    // Lost Items
    Route::get('/lost-items/my', [LostItemController::class, 'myItems']);
    Route::post('/lost-items/{id}/mark-found', [LostItemController::class, 'markFound']);
    Route::apiResource('lost-items', LostItemController::class);

    // Found Items
    Route::get('/found-items/my', [FoundItemController::class, 'myItems']);
    Route::post('/found-items/{id}/mark-claimed', [FoundItemController::class, 'markClaimed']);
    Route::apiResource('found-items', FoundItemController::class);

    // Matches
    Route::get('/matches/unviewed', [MatchController::class, 'unviewedCount']);
    Route::post('/matches/{match}/notify', [MatchController::class, 'notify']);
    Route::post('/matches/{match}/view', [MatchController::class, 'markAsViewed']);
    Route::apiResource('matches', MatchController::class)->only(['index', 'show']);

    // Returns
    Route::get('/returns/my', [ReturnController::class, 'myReturns']);
    Route::apiResource('returns', ReturnController::class)->except(['update', 'destroy']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto']);
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto']);
    Route::put('/password', [ProfileController::class, 'updatePassword']);

    // Categories & Metadata
    Route::get('/categories', function () {
        return response()->json([
            'categories' => [
                'wallet', 'phone', 'keys', 'bag', 'documents', 
                'electronics', 'jewelry', 'clothing', 'pet', 'other'
            ]
        ]);
    });
});
});
