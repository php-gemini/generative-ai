<?php

namespace PhpGemini\GenerativeAI;

use Exception;
use GuzzleHttp\Client;
use PhpGemini\GenerativeAI\Services\OAuth2Service;
use Illuminate\Support\Facades\Log;
use RuntimeException;



class GeminiClient
{
    public const DEFAULT_URL = 'https://generativelanguage.googleapis.com/v1beta';
    public const VISION_MODELS = ['gemini-1.5-flash', 'gemini-1.5-pro', 'gemini-2.5-flash', 'gemini-2.5-pro'];

    protected string $apiKey;
    protected string $model;
    protected Client $http;
    protected ?OAuth2Service $oauth2 = null;
    private ?string $systemInstruction = null;
    protected string $baseUrl = self::DEFAULT_URL;

    /**
     * GeminiClient constructor.
     *
     * @param array{
     *     api_key?: string,
     *     model?: string,
     *     base_url?: string,
     *     oauth2?: OAuth2Service,
     *     system_instruction?: string
     * } $config
     */
    public function __construct(array $config = [])
    {
        $apiKey = $config['api_key'] ?? $this->getConfigValue('gemini.api_key');
        $oauth2 = $config['oauth2'] ?? null;

        if (!$apiKey && !$oauth2) {
            throw new \InvalidArgumentException(
                'API key is required when OAuth2 is not provided.'
            );
        }
        $this->apiKey = (string) $apiKey;

        $model = $config['model'] ?? $this->getConfigValue('gemini.model');
        if (!$model) {
            throw new \InvalidArgumentException('Model is required.');
        }
        $this->model = (string) $model;

        $this->systemInstruction = $config['system_instruction'] ?? null;
        $this->oauth2            = $oauth2;
        $this->baseUrl           = rtrim($config['base_url'] ?? self::DEFAULT_URL, '/');

        $this->http = new Client([
            'base_uri' => "{$this->baseUrl}/models/",
            'timeout'  => 30.0,
        ]);
    }


    protected function request(string $endpoint, array $params = [], ?string $model = null): array
    {
        $model = $model ?? $this->model;
        $url = "{$model}%3A{$endpoint}";

        $headers = [
            'Authorization' => $this->oauth2 instanceof OAuth2Service
                ? 'Bearer ' . $this->oauth2->getAccessToken()
                : 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ];

        $response = $this->http->post($url, [
            'headers' => $headers,
            'json' => $params,
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function generateContent(string $prompt, bool $asArray = false, ?string $model = null): string|array
    {
        $currentModel = $model ?? $this->model;

        $messages = [];
        if ($this->systemInstruction) {
            $messages[] = [
                'role' => 'system',
                'parts' => [
                    ['text' => $this->systemInstruction],
                ],
            ];
        }

        $messages[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $prompt],
            ],
        ];

        $data = $this->request('generateContent', [
            'contents' => $messages
        ], $currentModel);

        return $asArray
            ? $data
            : $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
    }

    public function chat(array $messages, bool $asArray = false, ?string $model = null): string|array
    {
        $currentModel = $model ?? $this->model;
        $contents = array_map(fn($msg) => ['parts' => [['text' => $msg]]], $messages);

        $data = $this->request('generateContent', ['contents' => $contents], $currentModel);

        return $asArray
            ? $data
            : $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
    }

    public function generateContentWithImage(string $imagePath, string $prompt = '', ?string $model = null): string
    {
        $currentModel = $model ?? $this->model;

        if (!file_exists($imagePath)) {
            throw new Exception("Image file not found: $imagePath");
        }

        if (!str_contains($currentModel, 'vision') && !str_contains($currentModel, 'flash')) {
            throw new Exception("❌ Image input only supported for vision models. Current model: {$currentModel}");
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
        ], $currentModel);

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
        return str_contains($this->model, 'vision') || str_contains($this->model, 'flash');
    }

    public function generateContentFromImageUrl(string $imageUrl, string $prompt): array
    {

        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            throw new \RuntimeException("❌ Failed to download image from URL: {$imageUrl}");
        }

        if (!in_array($this->model, self::VISION_MODELS, true)) {
            throw new \RuntimeException("❌ Image input only supported for vision models. Current model: {$this->model}");
        }


        $imageData = @file_get_contents($imageUrl);
        if ($imageData === false) {
            throw new \RuntimeException("❌ Failed to download image from URL: {$imageUrl}");
        }

        $base64Image = base64_encode($imageData);

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => 'image/jpeg',
                                'data' => $base64Image,
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->request('generateContent', $payload, $this->model);

        return $response['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
    }

    public function setSystemInstruction(string $instruction): void
    {
        $this->systemInstruction = trim($instruction);
    }

    public function getSystemInstruction(): ?string
    {
        return $this->systemInstruction;
    }

    public static function fromLegacy(string $apiKey, string $model, ?OAuth2Service $oauth2 = null): self
    {
        return new self([
            'api_key' => $apiKey,
            'model' => $model,
            'oauth2' => $oauth2,
        ]);
    }

    protected function formatContentMessages(string|array $prompt): array
    {
        $messages = [];

        if ($this->systemInstruction) {
            $messages[] = [
                'role' => 'system',
                'parts' => [
                    ['text' => $this->systemInstruction],
                ],
            ];
        }

        $messages[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $prompt],
            ],
        ];

        return $messages;
    }

    private function getConfigValue(string $key): ?string
    {
        if (function_exists('config')) {
            try {
                return config($key);
            } catch (\Exception $e) {
                // Laravel config not available, continue
            }
        }

        if (function_exists('config') && !str_contains($key, '.')) {
            return config($key);
        }
        
        $envKey = strtoupper(str_replace('.', '_', $key));
        return $_ENV[$envKey] ?? getenv($envKey) ?: null;
    }

}
