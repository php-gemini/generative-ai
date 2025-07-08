# 💎 Gemini PHP Package Documentation

Welcome to the **Gemini PHP Package**, a lightweight and Laravel-friendly PHP SDK to interact with Google's Gemini Generative AI models.

This SDK supports:
- Text generation with Gemini models (e.g., `gemini-1.5-flash`, `gemini-2.5-pro`, etc.)
- Image input (vision) support (for compatible models)
- Easy configuration via `.env`
- Laravel service provider and facade integration
- Full support for OAuth2 authentication (via refresh token)
- Unit tested with PHPUnit and Testbench

---

## 📦 Installation

You can install the Package via Composer:

```bash
composer require php-gemini/generative-ai
```

---

## ⚙️ Configuration

Publish the configuration file to your Laravel project:

```bash
php artisan vendor:publish --provider="PhpGemini\GenerativeAI\Providers\GeminiServiceProvider" --tag="config"
```

This will create the config file at `config/gemini.php`. Edit the config file or your `.env` to add your API credentials.

The full `config/gemini.php` file looks like this:
```php
// config/gemini.php

return [
    'api_key' => env('GEMINI_API_KEY', ''),
    'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
    'oauth2' => [
        'client_id' => env('GEMINI_OAUTH_CLIENT_ID', null),
        'client_secret' => env('GEMINI_OAUTH_CLIENT_SECRET', null),
        'refresh_token' => env('GEMINI_OAUTH_REFRESH_TOKEN', null),
    ],
];
```

Update your `.env` file with the necessary credentials:
```dotenv
# For API Key Authentication
GEMINI_API_KEY=your_actual_api_key_here
GEMINI_MODEL=gemini-1.5-flash

# For OAuth2 Authentication
GEMINI_OAUTH_CLIENT_ID=your_client_id
GEMINI_OAUTH_CLIENT_SECRET=your_client_secret
GEMINI_OAUTH_REFRESH_TOKEN=your_refresh_token
```

---

## 🚀 Usage

### In Laravel (Facade)
Use the `Gemini` facade anywhere in your Laravel app:
```php
use Gemini;

$response = Gemini::generate('Say hello to the world.');
echo $response;
```

### In Plain PHP (with API Key)
You can also use the Package in plain PHP projects:
```php
<?php

require 'vendor/autoload.php';

use PhpGemini\GenerativeAI\GeminiClient;

$client = new GeminiClient([
    'api_key' => 'your_api_key',
    'model' => 'gemini-1.5-flash',
]);

echo $client->generateContent('Say hello to the world.');
```

### With OAuth2 (Plain PHP)
```php
<?php

require 'vendor/autoload.php';

use PhpGemini\GenerativeAI\GeminiClient;
use PhpGemini\GenerativeAI\Services\OAuth2Service;

$oauth2 = new OAuth2Service([
    'client_id' => 'your-client-id',
    'client_secret' => 'your-client-secret',
    'refresh_token' => 'your-refresh-token',
]);

$client = new GeminiClient([
    'model' => 'gemini-1.5-flash',
    'oauth2' => $oauth2,
]);

echo $client->generateContent('Say hello to the world.');
```

---

## ✨ Features

- **Text generation** — Send prompts to Gemini models and get AI-generated text.
- **Vision support** — Analyze images by sending base64 encoded images (supported models only).
- **Configurable models** — Switch between Gemini models easily.
- **Laravel Integration** — Service provider and facade for seamless use.
- **OAuth2 Support** — Secure, refresh-token-based access for server-to-server calls.
- **Unit tested** — Includes a full test suite using PHPUnit + Orchestra Testbench.
- **Error handling** — Built-in error detection and exceptions.

---

## 🔐 OAuth2 Token Generator

To generate a refresh token for OAuth2 usage, use our companion CLI tool. It walks you through the full flow and gives you `.env`-ready variables.

👉 **[Zero-Asif/oauth2_refresh_token_generator](https://github.com/Zero-Asif/oauth2_refresh_token_generator)**

---

## 🤝 Contributing

Feel free to open issues and pull requests. Contributions are welcome!

---

## 📄 License

This SDK is licensed under the **MIT License**. See the `LICENSE` file for details.

---

## 🔗 Links

- **GitHub Repository:** [php-gemini/generative-ai](https://github.com/php-gemini/generative-ai)
- **Packagist Package:** [php-gemini/generative-ai](https://packagist.org/packages/php-gemini/generative-ai)
- **Official Gemini API Docs:** [https://ai.google.dev](https://ai.google.dev)
- **OAuth2 Token Generator Tool:** [Zero-Asif/oauth2_refresh_token_generator](https://github.com/Zero-Asif/oauth2_refresh_token_generator)
- **Google Cloud Console (OAuth Credentials):** [https://console.cloud.google.com/apis/credentials](https://console.cloud.google.com/apis/credentials)

---

## 👨‍💻 Contact

If you need help or want to discuss features, reach out at:
- **Email:** asifuzzamanasif0001@gmail.com
- **GitHub:** [Zero-Asif](https://github.com/Zero-Asif)
