<?php

namespace PhpGemini\GenerativeAI\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Dotenv\Dotenv;


abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \PhpGemini\GenerativeAI\Providers\GeminiServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Gemini' => \PhpGemini\GenerativeAI\Facades\Gemini::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('gemini.api_key', 'test-api-key');
        $app['config']->set('gemini.model', 'gemini-1.5-flash');
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (file_exists(__DIR__ . '/../.env')) {
            Dotenv::createImmutable(__DIR__ . '/../')->load();
        }
    }
}
