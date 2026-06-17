<?php

namespace App\Providers;

use App\Contracts\AuthServiceInterface;
use App\Contracts\BoundaryServiceInterface;
use App\Contracts\FavoriteLocationServiceInterface;
use App\Contracts\GeocodingServiceInterface;
use App\Contracts\WeatherServiceInterface;
use App\Services\AuthService;
use App\Services\FavoriteLocationService;
use App\Services\NominatimService;
use App\Services\OpenMeteoService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * Maps contract interfaces to their concrete implementations.
     * This allows controllers to type-hint interfaces instead of
     * concrete classes, enabling easier testing and swappability.
     *
     * @var array<string, string>
     */
    public array $bindings = [
        AuthServiceInterface::class             => AuthService::class,
        WeatherServiceInterface::class          => OpenMeteoService::class,
        GeocodingServiceInterface::class        => OpenMeteoService::class,
        BoundaryServiceInterface::class         => NominatimService::class,
        FavoriteLocationServiceInterface::class  => FavoriteLocationService::class,
    ];

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
        //
    }
}
