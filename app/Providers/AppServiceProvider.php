<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Order;
use App\Models\SubCategory;
use App\Observers\CategoryObserver;
use App\Observers\OrderObserver;
use App\Observers\SubCategoryObserver;
use Illuminate\Support\ServiceProvider;

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
        Order::observe(OrderObserver::class);
        Category::observe(CategoryObserver::class);
        SubCategory::observe(SubCategoryObserver::class);
    }
}
