<?php

namespace PhpGemini\GenerativeAI\Providers;

use Illuminate\Support\ServiceProvider;
use PhpGemini\GenerativeAI\GeminiClient;

class GeminiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/gemini.php', 'gemini');

        $this->app->singleton(GeminiClient::class, function ($app) {
            $config = $app['config']->get('gemini');
            return new GeminiClient($config['api_key'], $config['model']);
        });

        $this->app->alias(GeminiClient::class, 'gemini');
    }

    public function boot()
    {
        $configPath = function_exists('config_path')
            ? config_path('gemini.php')
            : __DIR__ . '/../../config/gemini.php';
        $this->publishes([
             __DIR__.'/../../config/gemini.php' => $configPath,
        ], 'config');
    }
}
