<?php

namespace PhpGemini\GenerativeAI;

use Exception;
use GuzzleHttp\Client;
use PhpGemini\GenerativeAI\Services\OAuth2Service;



class GeminiClient
{
    protected string $apiKey;
    protected string $model;
    protected Client $http;
    protected ?OAuth2Service $oauth2Service;

    public function __construct(
        string $apiKey = '',
        string $model = '',
        ?OAuth2Service $oauth2Service = null
    ) {
        $this->apiKey = $apiKey ?: config('gemini.api_key');
        $this->model = $model ?: config('gemini.model');
        $this->oauth2Service = $oauth2Service;

        $this->http = new Client([
            'base_uri' => 'https://generativelanguage.googleapis.com/v1beta/models/',
            'timeout'  => 30.0,
        ]);
    }

    private function request(string $endpoint, array $body): array
    {
        $url = "{$this->model}:$endpoint";

        $options = [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $body,
        ];

        if ($this->oauth2Service) {
            $accessToken = $this->oauth2Service->getAccessToken();
            $options['headers']['Authorization'] = 'Bearer ' . $accessToken;
        } else {
            $url .= '?key=' . $this->apiKey;
        }

        $response = $this->http->post($url, $options);
        $data = json_decode($response->getBody(), true);

        if (isset($data['error'])) {
            throw new Exception("Gemini API Error: " . $data['error']['message']);
        }

        return $data;
    }

    public function generateContent(string $prompt, bool $asArray = false): string|array
    {
        $data = $this->request('generateContent', [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ]);

        return $asArray
            ? $data
            : $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
    }

    public function chat(array $messages, bool $asArray = false): string|array
    {
        $contents = array_map(fn($msg) => ['parts' => [['text' => $msg]]], $messages);

        $data = $this->request('generateContent', ['contents' => $contents]);

        return $asArray
            ? $data
            : $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
    }

    public function generateContentWithImage(string $imagePath, string $prompt = ''): string
    {
        if (!file_exists($imagePath)) {
            throw new Exception("Image file not found: $imagePath");
        }

        if (!$this->supportsVision()) {
            throw new Exception("❌ Image input only supported for vision models. Current model: {$this->model}");
        }

        $imageData = base64_encode(file_get_contents($imagePath));
        $mimeType = mime_content_type($imagePath);

        $data = $this->request('generateContent', [
            'contents' => [[
                'parts' => [
                    ['text' => $prompt],
                    [
                        'inline_data' => [
                            'mime_type' => $mimeType,
                            'data' => $imageData
                        ]
                    ]
                ]
            ]]
        ]);

        return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
    }

    public function generateImageFromText(string $prompt, int $width = 512, int $height = 512, bool $asArray = false): string|array
    {
        $data = $this->request('generateImage', [
            'prompt' => ['text' => $prompt],
            'imageConfig' => [
                'height' => $height,
                'width' => $width,
                'mimeType' => 'image/png',
            ]
        ]);

        return $asArray
            ? $data
            : $data['artifacts'][0]['imageUri'] ?? 'No image generated';
    }

    public function getEmbeddings(string $input, bool $asArray = false): array|string
    {
        $data = $this->request('embedText', ['text' => $input]);

        return $asArray
            ? $data
            : $data['embedding'] ?? [];
    }

    public function supportsVision(): bool
    {
        return str_contains($this->model, 'vision');
    }
}
