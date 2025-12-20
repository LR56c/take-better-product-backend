<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Brands\Domain\Contracts\BrandRepository;
use Src\Brands\Infrastructure\EloquentBrandRepository;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Infrastructure\EloquentCategoryRepository;
use Src\Countries\Domain\CountryRepository;
use Src\Countries\Infrastructure\EloquentCountryRepository;
use Src\Products\Domain\Contracts\ProductRepository;
use Src\Products\Infrastructure\EloquentProductRepository;
use Src\Stores\Domain\StoreRepository;
use Src\Stores\Infrastructure\EloquentStoreRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepository::class, EloquentProductRepository::class);
        $this->app->bind(BrandRepository::class, EloquentBrandRepository::class);
        $this->app->bind(CountryRepository::class, EloquentCountryRepository::class);
        $this->app->bind(CategoryRepository::class, EloquentCategoryRepository::class);
        $this->app->bind(StoreRepository::class, EloquentStoreRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
