<?php

namespace App\Integration;

use Illuminate\Support\ServiceProvider;
use App\Integration\LaravelAI;

/**
 * Laravel service provider for PHP and Laravel code classifier.
 */
class LaravelAIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/laravel-ai.php', 'laravel-ai'
        );

        $this->app->singleton('laravel-ai', function ($app) {
            $config = $app['config']['laravel-ai'];
            $laravelAI = new LaravelAI();
            
            if (isset($config['model_path']) && file_exists($config['model_path'])) {
                $laravelAI->loadModel($config['model_path']);
            } else {
                $laravelAI->createModel(
                    $config['model_type'] ?? 'random_forest',
                    $config['model_params'] ?? []
                );
            }
            
            return $laravelAI;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/laravel-ai.php' => config_path('laravel-ai.php'),
        ], 'config');
    }
}
