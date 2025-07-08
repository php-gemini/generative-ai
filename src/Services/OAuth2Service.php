<?php
namespace PhpGemini\GenerativeAI\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Google\Auth\OAuth2;

class OAuth2Service
{
    private OAuth2 $oauth2;
    protected string $clientId;
    protected string $clientSecret;
    protected string $refreshToken;
    protected ?string $accessToken = null;
    protected int $tokenExpiresAt = 0;

    public function __construct(array $config)
    {
        $this->oauth2 = new OAuth2([
            'clientId' => $config['client_id'],
            'clientSecret' => $config['client_secret'],
            'refreshToken' => $config['refresh_token'],
            'tokenCredentialUri' => $config['token_uri'] ?? 'https://oauth2.googleapis.com/token',
        ]);
    }

    public function getAccessToken(): string
    {
        $token = $this->oauth2->fetchAuthToken();
        if (isset($token['error'])) {
            throw new \Exception('OAuth2 error: ' . ($token['error_description'] ?? 'unknown'));
        }
        return $token['access_token'] ?? '';
    }

    protected function refreshAccessToken(): void
    {
        $client = new Client([
            'base_uri' => 'https://oauth2.googleapis.com/',
            'timeout'  => 10,
        ]);

        try {
            $response = $client->post('token', [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $this->refreshToken,
                    'grant_type' => 'refresh_token',
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            if (empty($data['access_token'])) {
                throw new \RuntimeException("Failed to get access token from OAuth2 refresh response.");
            }

            $this->accessToken = $data['access_token'];
            
            $this->tokenExpiresAt = time() + ($data['expires_in'] ?? 3600) - 60; 

        } catch (RequestException $e) {
            
            $msg = "OAuth2 token refresh failed: ";
            if ($e->hasResponse()) {
                $msg .= (string)$e->getResponse()->getBody();
            } else {
                $msg .= $e->getMessage();
            }
            throw new \RuntimeException($msg);
        }
    }
}
