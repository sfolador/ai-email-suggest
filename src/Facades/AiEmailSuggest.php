<?php

namespace Sfolador\AiEmailSuggest\Facades;

use Illuminate\Support\Facades\Facade;
use Sfolador\AiEmailSuggest\AiEmailSuggestFake;
use Sfolador\AiEmailSuggest\AiEmailSuggestInterface;

/**
 * @see \Sfolador\AiEmailSuggest\AiEmailSuggest
 */
class AiEmailSuggest extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AiEmailSuggestInterface::class;
    }

    public static function fake(): void
    {
        static::swap(new AiEmailSuggestFake());
    }
}
