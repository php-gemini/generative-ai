<?php

namespace PhpGemini\GenerativeAI\Facades;

use Illuminate\Support\Facades\Facade;

class Gemini extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'gemini'; 
    }
}
