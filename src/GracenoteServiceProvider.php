<?php

namespace Atomescrochus\Gracenote;

use Atomescrochus\Gracenote\Gracenote;
use Illuminate\Support\ServiceProvider;

class GracenoteServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/laravel-gracenote.php' => config_path('laravel-gracenote.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/config/laravel-gracenote.php', 'laravel-gracenote');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\GetGracenoteUserId::class,
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSkeleton();
        
        config([
                'config/laravel-gracenote.php',
        ]);
    }

    private function registerSkeleton()
    {
        $this->app->bind('gracenote', function () {
            return new Gracenote();
        });
    }
}
