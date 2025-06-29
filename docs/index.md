# Gemini PHP Package Documentation

---

## Introduction

Welcome to the **Gemini PHP Package**, a lightweight and Laravel-friendly PHP package to interact with Google's Gemini Generative AI models.

This SDK supports:

* Text generation with Gemini models (e.g., `gemini-1.5-flash`, `gemini-2.5-pro`, etc.)
* Image input (vision) support (for compatible models)
* Easy configuration via `.env`
* Laravel service provider and facade integration
* Future support for OAuth2 authentication

---

## Installation

You can install the Package via Composer:

```bash
composer require php-ai/gemini-generative-ai
```

### Configuration

Publish the configuration file to your Laravel project:

```bash
php artisan vendor:publish --provider="PhpAi\Gemini\Providers\GeminiServiceProvider" --tag="config"
```

This will create the config file at `config/gemini.php`.

Edit the config file or your `.env` to add your API credentials:

```php
// config/gemini.php

return [
    'api_key' => env('GEMINI_API_KEY', ''),
    'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
];
```

Add your API key and optionally the model in your `.env` file:

```env
GEMINI_API_KEY=your_actual_api_key_here
GEMINI_MODEL=gemini-1.5-flash
```

---

## Usage

### Laravel (Facade)

Use the `Gemini` facade anywhere in your Laravel app:

```php
use Gemini;

$response = Gemini::generate('Say hello to the world.');
echo $response;
```

### Plain PHP

You can also use the Package in plain PHP projects:

```php
<?php

require 'vendor/autoload.php';

use PhpAi\Gemini\GeminiClient;

$client = new GeminiClient('your_api_key', 'gemini-1.5-flash');
echo $client->generateContent('Say hello to the world.');
```

---

## Features

* **Text generation** — send prompts to Gemini models and get AI-generated text.
* **Vision support** — analyze images by sending base64 encoded images (supported models only).
* **Configurable models** — switch between Gemini models easily.
* **Laravel Integration** — service provider and facade for seamless use.
* **Future OAuth2 Support** — planned for secure, OAuth2 authenticated requests.
* **Error handling** — built-in error detection and exceptions.

---

## Contributing

Feel free to open issues and pull requests. Contributions are welcome!

## License

This SDK is licensed under the MIT License. See the `LICENSE` file for details.

## Links

* [GitHub Repository](https://github.com/php-ai/gemini-generative-ai)
* [Packagist Package](https://packagist.org/packages/php-ai/gemini-generative-ai)
* [Official Gemini API Docs](https://ai.google.dev/docs)

## Contact

If you need help or want to discuss features, reach out at:

* **Email:** asifuzzamanasif0001@gmail.com
* **GitHub:** [Zero-Asif](https://github.com/Zero-Asif)
