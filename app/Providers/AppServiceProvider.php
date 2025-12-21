<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Products\Domain\ProductRepository;
use Src\Products\Infrastructure\EloquentProductRepository;
use Src\Brands\Domain\BrandRepository;
use Src\Brands\Infrastructure\EloquentBrandRepository;
use Src\Countries\Domain\CountryRepository;
use Src\Countries\Infrastructure\EloquentCountryRepository;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Infrastructure\EloquentCategoryRepository;
use Src\Stores\Domain\StoreRepository;
use Src\Stores\Infrastructure\EloquentStoreRepository;
use Src\Users\Domain\UserRepository;
use Src\Users\Infrastructure\EloquentUserRepository;

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
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
