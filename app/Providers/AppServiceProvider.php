<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

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
        View::composer('partials.navbar', static function (ViewInstance $view): void {
            $navigationCategories = Category::query()
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->with(['children' => static function (HasMany $query): void {
                    $query->where('is_active', true)->orderBy('name');
                }])
                ->orderBy('name')
                ->get();

            $view->with('navigationCategories', $navigationCategories);
        });
    }
}
