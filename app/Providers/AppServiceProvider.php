<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\ItemMatch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share unviewed matches count with all views
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $userId = auth()->id();
                
                $unviewedMatchesCount = ItemMatch::where(function($query) use ($userId) {
                    $query->whereHas('lostItem', function($q) use ($userId) {
                        $q->where('user_id', $userId);
                    })->whereNull('lost_user_viewed_at')
                    ->orWhere(function($q) use ($userId) {
                        $q->whereHas('foundItem', function($subQ) use ($userId) {
                            $subQ->where('user_id', $userId);
                        })->whereNull('found_user_viewed_at');
                    });
                })->count();
                
                $view->with('unviewedMatchesCount', $unviewedMatchesCount);
            }
        });
    }
}
