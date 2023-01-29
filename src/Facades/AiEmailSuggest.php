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
    protected static function getFacadeAccessor()
    {
        return AiEmailSuggestInterface::class;
    }

    public static function fake()
    {
        static::swap(new AiEmailSuggestFake());
    }
}
