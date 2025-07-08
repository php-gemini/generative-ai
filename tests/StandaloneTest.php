<?php

namespace PhpGemini\GenerativeAI\Tests;

use PhpGemini\GenerativeAI\GeminiClient;
use PHPUnit\Framework\TestCase;
use PhpGemini\GenerativeAI\Services\OAuth2Service;

class StandaloneTest extends TestCase
{
    public function test_client_instantiates_successfully()
    {
        $client = new GeminiClient([
            'api_key' => 'fake-api-key',
            'model' => 'gemini-1.5-flash',
        ]);
        $this->assertInstanceOf(GeminiClient::class, $client);
    }

    public function test_it_throws_exception_without_api_key()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('API key is required when OAuth2 is not provided.');
        
        new GeminiClient([
            'model' => 'gemini-1.5-flash'
        ]);
    }

    public function test_it_throws_exception_without_model()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Model is required.');
        
        new GeminiClient([
            'api_key' => 'fake-api-key'
        ]);
    }

    public function test_model_switching_works_correctly(): void
    {
        $client = new GeminiClient([
            'api_key' => 'test-key',
            'model' => 'model-A',
        ]);
        $this->assertEquals('model-A', $client->getModel());

        $client->setModel('model-B');
        $this->assertEquals('model-B', $client->getModel());
    }

    public function test_client_works_with_oauth2_only(): void
    {
        $oauthMock = $this->createMock(\PhpGemini\GenerativeAI\Services\OAuth2Service::class);
        $oauthMock->method('getAccessToken')->willReturn('fake-bearer-token');

        $client = new GeminiClient([
            'model' => 'gemini-1.5-flash',
            'oauth2' => $oauthMock,
        ]);

        $this->assertInstanceOf(GeminiClient::class, $client);
        $this->assertEquals('gemini-1.5-flash', $client->getModel());
    }

    public function test_system_instruction_setting(): void
    {
        $client = new GeminiClient([
            'api_key' => 'test-key',
            'model' => 'gemini-1.5-flash',
        ]);

        $client->setSystemInstruction("You are a helpful assistant");
        $this->assertEquals("You are a helpful assistant", $client->getSystemInstruction());
    }
}