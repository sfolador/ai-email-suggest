<?php

namespace Sfolador\AiEmailSuggest\Facades;

use Illuminate\Support\Facades\Facade;
use Sfolador\AiEmailSuggest\Services\AiServiceFake;
use Sfolador\AiEmailSuggest\Services\AiServiceInterface;

class AiService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AiServiceInterface::class;
    }

    public static function fake()
    {
        static::swap(new AiServiceFake());
    }
}
