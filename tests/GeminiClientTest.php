<?php

namespace PhpGemini\GenerativeAI\Tests;

use PhpGemini\GenerativeAI\GeminiClient;
use PHPUnit\Framework\TestCase;
use PhpGemini\GenerativeAI\Services\OAuth2Service;

class GeminiClientTest extends TestCase
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

    public function test_generate_content_with_image_throws_if_file_missing(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Image file not found');

        $client = new GeminiClient([
            'api_key' => 'test-key',
            'model' => 'gemini-1.5-flash',
        ]);
        $client->generateContentWithImage('invalid_path.jpg', 'Describe this');
    }

    public function test_generate_content_with_image_throws_if_model_not_supported(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('❌ Image input only supported for vision models');

        $imagePath = sys_get_temp_dir() . '/test_sample.jpg';
        file_put_contents($imagePath, 'fake-image-data');

        $client = new GeminiClient([
            'api_key' => 'test-key', 
            'model' => 'gemini-pro'
        ]); 
        
        try {
            $client->generateContentWithImage($imagePath, 'Describe this');
        } finally {
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    }

    public function test_oauth2_bearer_header_is_used(): void
    {
        $container = [];
        $history = \GuzzleHttp\Middleware::history($container);

        $mock = new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(200, [], json_encode([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'OK']]]]
                ]
            ])),
        ]);

        $stack = \GuzzleHttp\HandlerStack::create($mock);
        $stack->push($history);

        $httpClient = new \GuzzleHttp\Client(['handler' => $stack]);

        $oauthMock = $this->createMock(\PhpGemini\GenerativeAI\Services\OAuth2Service::class);
        $oauthMock->method('getAccessToken')->willReturn('fake-bearer');

        $client = new \PhpGemini\GenerativeAI\GeminiClient([
            'api_key' => 'ignored-key',
            'model' => 'gemini-1.5-flash',
            'oauth2' => $oauthMock,
        ]);

        $reflection = new \ReflectionProperty($client, 'http');
        $reflection->setAccessible(true);
        $reflection->setValue($client, $httpClient);

        $client->generateContent('Hello!');

        $this->assertNotEmpty($container);
        $request = $container[0]['request'];
        $this->assertSame('Bearer fake-bearer', $request->getHeaderLine('Authorization'));
    }

    public function test_generate_content_from_image_url_fails_for_invalid_url(): void
    {
        $client = new GeminiClient([
            'model' => 'gemini-1.5-flash',
            'api_key' => 'dummy-key',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Failed to download image from URL/');

        $client->generateContentFromImageUrl(
            'https://invalid.example.com/image.jpg',
            'Say what you see'
        );
    }

    public function test_generate_content_from_image_url_throws_if_model_not_supported(): void
    {
        $client = new GeminiClient([
            'api_key' => 'test-key',
            'model' => 'gemini-pro',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('only supported for vision models');

        $client->generateContentFromImageUrl('https://example.com/image.jpg', 'What do you see?');
    }

    public function test_system_instruction_is_injected(): void
    {
        $client = new GeminiClient([
            'api_key' => 'test-key',
            'model' => 'gemini-1.5-flash',
        ]);

        $client->setSystemInstruction("You are a helpful assistant");

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('formatContentMessages');
        $method->setAccessible(true);

        $result = $method->invoke($client, 'Hello!');

        $this->assertIsArray($result);
        $this->assertEquals('system', $result[0]['role']);
        $this->assertStringContainsString('helpful assistant', $result[0]['parts'][0]['text']);
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
}