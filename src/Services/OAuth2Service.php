<?php

namespace PhpGemini\GenerativeAI\Services;

use Google\Auth\OAuth2;

class OAuth2Service
{
    protected OAuth2 $oauth2;

    public function __construct(array $config)
    {
        $this->oauth2 = new OAuth2($config);
    }

    public function getAccessToken(): string
    {
        $token = $this->oauth2->fetchAuthToken();
        return $token['access_token'] ?? '';
    }
}
