<?php

namespace PhpGemini\GenerativeAI\Providers;

use Illuminate\Support\ServiceProvider;
use PhpGemini\GenerativeAI\GeminiClient;
use PhpGemini\GenerativeAI\Services\OAuth2Service;

class GeminiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/gemini.php', 'gemini');

        $this->app->singleton(OAuth2Service::class, function ($app) {
            return new OAuth2Service($app['config']->get('gemini.oauth'));
        });

        $this->app->singleton(GeminiClient::class, function ($app) {
            $cfg = $app['config']->get('gemini');
            return new GeminiClient(
                $cfg['api_key'],
                $cfg['model'],
                $app->make(OAuth2Service::class) 
            );
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
