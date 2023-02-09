<?php

namespace Sfolador\AiEmailSuggest\Facades;

use Illuminate\Support\Facades\Facade;
use Sfolador\AiEmailSuggest\Services\AiServiceFake;
use Sfolador\AiEmailSuggest\Services\AiServiceInterface;

class AiService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AiServiceInterface::class;
    }

    public static function fake(): void
    {
        static::swap(new AiServiceFake());
    }
}
