<?php

namespace Sfolador\AiEmailSuggest\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sfolador\AiEmailSuggest\AiEmailSuggest
 */
class AiEmailSuggest extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Sfolador\AiEmailSuggest\AiEmailSuggest::class;
    }
}
