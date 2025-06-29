# 🧠 Gemini PHP SDK for Google Generative AI

[![Packagist](https://img.shields.io/packagist/v/php-gemini/generative-ai)](https://packagist.org/packages/php-gemini/generative-ai)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-^8.0-blue.svg)](https://php.net)

A Laravel-friendly, lightweight PHP SDK to interact with [Google Gemini Generative AI](https://ai.google.dev/). Supports text generation, image input (vision), and is fully customizable for any PHP project.

---

## ✨ Features

- ✅ Gemini text generation (prompt → response)
- 🖼️ Image input support (base64 vision models)
- ⚙️ Configurable model & API key via `.env`
- 🎯 Laravel-ready (service provider + facade)
- 🔒 OAuth2 support planned
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

Then update your `.env` file:

```dotenv
GEMINI_API_KEY=your_actual_api_key
GEMINI_MODEL=gemini-1.5-flash
```
You can also update `config/gemini.php` as needed.

---

## 🚀 Usage

### ✅ In Laravel

```php
use Gemini;

echo Gemini::generate("What's the future of AI?");
```

### ✅ In Plain PHP

```php
use PhpGemini\GenerativeAI\GeminiClient;

require 'vendor/autoload.php';

$gemini = new GeminiClient('your_api_key', 'gemini-1.5-flash');
echo $gemini->generateContent("Tell me something interesting.");
```

### 🖼️ Image Input (Vision Model)

```php
$response = $gemini->generateContentWithImage('path/to/image.jpg', 'What’s in this image?');
```
> ⚠️ **Note:** Only works with models that support image input, like `gemini-1.5-flash` or `gemini-pro-vision`.

---

## 🛠️ Roadmap

- [x] Text generation
- [x] Laravel integration
- [x] Image input (vision support)
- [ ] Streaming response support
- [ ] OAuth2 integration
- [ ] Unit testing & mocking
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

This project is open-sourced under the MIT license.

---

## 🔗 Useful Links

- **Packagist:** [php-ai/gemini-generative-ai](https://packagist.org/packages/php-gemini/generative-ai)
- **Gemini API Docs:** [https://ai.google.dev](https://ai.google.dev)
- **Documentation:** `docs/index.md`

---

## 🙋 Author

**Zero-Asif (Asifuzzaman Asif)**
- 📧 **Email:** asifuzzamanasif0001@gmail.com
- 🔗 **GitHub:** [github.com/Zero-Asif](https://github.com/Zero-Asif)

✨ If this project helped you, please ⭐ the repo!
