<?php

if (!function_exists('config_path')) {
    function config_path($path = '')
    {
        return __DIR__ . '/../../config' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        return getenv(strtoupper(str_replace('.', '_', $key))) ?: $default;
    }
}
