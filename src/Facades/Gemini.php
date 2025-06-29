<?php

namespace PhpAi\Gemini\Facades;

use Illuminate\Support\Facades\Facade;

class Gemini extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'gemini'; 
    }
}
