<?php

if (!function_exists('config_path')) {
    function config_path($path = '')
    {
        return __DIR__ . '/../../config' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('gemini_config')) {
    function gemini_config($key = null, $default = null)
    {
        if (function_exists('config') && class_exists('\Illuminate\Support\Facades\App')) {
            try {
                return config($key, $default);
            } catch (\Exception $e) {
                // Laravel not available, fallback to environment
            }
        }

        if ($key) {
            $envKey = strtoupper(str_replace('.', '_', $key));
            return $_ENV[$envKey] ?? getenv($envKey) ?: $default;
        }

        return $default;
    }
}
