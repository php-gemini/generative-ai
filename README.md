# 🧠 Gemini PHP SDK for Google Generative AI

[![Packagist](https://img.shields.io/packagist/v/php-gemini/generative-ai)](https://packagist.org/packages/php-gemini/generative-ai)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-^8.0-blue.svg)](https://php.net)

A Laravel-friendly, lightweight PHP SDK to interact with [Google Gemini Generative AI](https://ai.google.dev/). Supports text generation, image input (vision), and is fully customizable for any PHP project.

---

## ✨ Features

- ✅ Gemini text generation (prompt → response)
- 🖼️ Image input support (base64 vision models)
- 🔐 Full OAuth2 authentication (refresh token supported)
- 🔧 Easy configuration via `.env` or constructor
- 🧪 PHPUnit + Testbench testing support
- 🎯 Laravel-ready (service provider + facade)
- 📦 Composer installable
- 📘 Full documentation included

---

## 🧪 Installation

Install via Composer:

```bash
composer require php-gemini/generative-ai
```

### ⚙️ Configuration

For Laravel users, publish the configuration file:

```bash
php artisan vendor:publish --provider="PhpGemini\GenerativeAI\Providers\GeminiServiceProvider"
```

Then update your `.env` file with your API Key:

```dotenv
GEMINI_API_KEY=your_actual_api_key
GEMINI_MODEL=gemini-1.5-flash
```
> 🔐 **Using OAuth2?** See the section below on generating and using refresh tokens instead.

---

## 🔐 OAuth2 Support

This SDK supports Google OAuth2 access via a `refresh_token`, `client_id`, and `client_secret`.

### 🔧 Generate Refresh Token
To generate your credentials securely, use our standalone command-line script:
**[Zero-Asif/oauth2_refresh_token_generator](https://github.com/Zero-Asif/oauth2_refresh_token_generator)**

Follow the instructions in its `README` file. After running the script, add the output to your `.env` file:

```dotenv
GEMINI_OAUTH_CLIENT_ID=your_client_id
GEMINI_OAUTH_CLIENT_SECRET=your_client_secret
GEMINI_OAUTH_REFRESH_TOKEN=your_refresh_token
```
> ✅ The SDK will automatically use OAuth2 if an API key is not provided.

---

## 🚀 Usage

### ✅ In Laravel
```php
use Gemini;

echo Gemini::generate("What's the future of AI?");
```

### ✅ In Plain PHP

Initialize with an **API Key**:
```php
use PhpGemini\GenerativeAI\GeminiClient;

require 'vendor/autoload.php';

$gemini = new GeminiClient([
    'api_key' => 'your_api_key',
    'model' => 'gemini-1.5-flash',
]);

echo $gemini->generateContent("Tell me something interesting.");
```

Or initialize with **OAuth2**:
```php
use PhpGemini\GenerativeAI\GeminiClient;
use PhpGemini\GenerativeAI\Services\OAuth2Service;

require 'vendor/autoload.php';

$oauth = new OAuth2Service([
    'client_id' => 'your_client_id',
    'client_secret' => 'your_client_secret',
    'refresh_token' => 'your_refresh_token',
]);

$gemini = new GeminiClient([
    'model' => 'gemini-1.5-flash',
    'oauth2' => $oauth
]);

echo $gemini->generateContent("Tell me something interesting.");
```

### 🖼️ Image Input (Vision Model)
```php
$response = $gemini->generateContentWithImage('path/to/image.jpg', 'What’s in this image?');
```
> ⚠️ **Note:** Only supported for models that accept image input, like `gemini-1.5-flash`.

---

## 🧪 Testing (PHPUnit)

To run the test suite:
```bash
composer test
```

To use Testbench for testing within a Laravel application, first install it:
```bash
composer require --dev orchestra/testbench
```

---

## 🛠️ Roadmap

- [x] Text generation
- [x] Laravel integration
- [x] Image input (vision support)
- [x] OAuth2 integration (refresh token support)
- [x] Unit testing & mocking (PHPUnit + Testbench)
- [ ] Streaming response support
- [ ] Online documentation site (mkdocs)

---

## 📘 Documentation

Full documentation is available inside: `docs/index.md`

---

## 🤝 Contributing

Pull requests, issues, and feature suggestions are welcome!

```bash
git clone [https://github.com/php-gemini/generative-ai.git](https://github.com/php-gemini/generative-ai.git)
cd generative-ai
composer install
```

---

## 📄 License

This project is open-sourced under the **MIT License**.

---

## 🔗 Useful Links

- **Packagist:** [php-gemini/generative-ai](https://packagist.org/packages/php-gemini/generative-ai)
- **Gemini API Docs:** [https://ai.google.dev](https://ai.google.dev)
- **OAuth2 Refresh Token Tool:** [Zero-Asif/oauth2_refresh_token_generator](https://github.com/Zero-Asif/oauth2_refresh_token_generator)
- **Google Cloud Console (OAuth Credentials):** [https://console.cloud.google.com/apis/credentials](https://console.cloud.google.com/apis/credentials)
- **Project Homepage:** [https://github.com/php-gemini/generative-ai](https://github.com/php-gemini/generative-ai)

---

## 🙋 Author

**Zero-Asif (Asifuzzaman Asif)**
- 📧 **Email:** asifuzzamanasif0001@gmail.com
- 🔗 **GitHub:** [github.com/Zero-Asif](https://github.com/Zero-Asif)

✨ If this project helped you, please ⭐ the repo!
