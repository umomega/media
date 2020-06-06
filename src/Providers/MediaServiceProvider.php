<?php

namespace Umomega\Media\Providers;

use Illuminate\Support\ServiceProvider;

class MediaServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/media.php', 'media'
        );

        require_once(__DIR__ . '/../Support/helpers.php');
    }

    /**
     * Register any media services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/../../config/media.php' => config_path('media.php')], 'config');

        $this->publishes([__DIR__ . '/../../resources/lang' => resource_path('lang/vendor/media')], 'lang');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'media');

        $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
    }

}
