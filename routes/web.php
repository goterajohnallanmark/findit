<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LostItemController;
use App\Http\Controllers\FoundItemController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes (accessible without authentication)
Route::get('/', function () {
    return view('welcome');
});

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Lost Items Routes
    Route::resource('lost-items', LostItemController::class)->names([
        'index' => 'lost-items.index',
        'create' => 'lost-items.create',
        'store' => 'lost-items.store',
        'show' => 'lost-items.show',
        'edit' => 'lost-items.edit',
        'update' => 'lost-items.update',
        'destroy' => 'lost-items.destroy',
    ]);

    // Found Items Routes
    Route::resource('found-items', FoundItemController::class)->names([
        'index' => 'found-items.index',
        'create' => 'found-items.create',
        'store' => 'found-items.store',
        'show' => 'found-items.show',
        'edit' => 'found-items.edit',
        'update' => 'found-items.update',
        'destroy' => 'found-items.destroy',
    ]);

    // Matches Routes
    Route::prefix('matches')->name('matches.')->group(function () {
        Route::get('/', [MatchController::class, 'index'])->name('index');
        Route::get('/{match}', [MatchController::class, 'show'])->name('show');
        Route::post('/{match}/notify', [MatchController::class, 'notify'])->name('notify');
    });

    // Returns Routes
    Route::prefix('returns')->name('returns.')->group(function () {
        Route::get('/', [ReturnController::class, 'index'])->name('index');
        Route::get('/create', [ReturnController::class, 'create'])->name('create');
        Route::post('/', [ReturnController::class, 'store'])->name('store');
        Route::get('/{id}', [ReturnController::class, 'show'])->name('show');
    });

    // Search Routes
    Route::prefix('search')->name('search.')->group(function () {
        Route::get('/', [SearchController::class, 'index'])->name('index');
        Route::get('/results', [SearchController::class, 'results'])->name('results');
    });

    // Profile & Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('profile.settings');

    // Additional Item Actions
    Route::post('/lost-items/{id}/mark-found', [LostItemController::class, 'markFound'])->name('lost-items.mark-found');
    Route::post('/found-items/{id}/mark-claimed', [FoundItemController::class, 'markClaimed'])->name('found-items.mark-claimed');
    
    // Test email notification route
    Route::get('/test-email', function () {
        $notifier = app(\App\Services\NotificationService::class);
        
        $item = [
            'name' => 'Test Item - ' . now()->format('H:i:s'),
            'description' => 'This is a test notification from FindIt',
            'id' => 99999,
            'link' => route('dashboard'),
        ];
        
        $result = $notifier->notifyMatch(
            auth()->user()->email,
            $item,
            [
                'matched_date' => now()->toDateTimeString(),
                'finder_name' => 'Test User',
            ]
        );
        
        return response()->json([
            'message' => 'Test email sent!',
            'result' => $result,
            'check_inbox' => auth()->user()->email,
        ]);
    })->name('test.email');
});

// Note: Authentication routes (login, register, logout) are typically handled by Laravel Breeze, Jetstream, or custom auth controllers
// These would be added separately based on your authentication setup
require __DIR__.'/auth.php';
