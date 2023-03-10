<?php

namespace Sfolador\AiEmailSuggest;

use OpenAI;
use Sfolador\AiEmailSuggest\Commands\AiSuggestClearCache;
use Sfolador\AiEmailSuggest\Middleware\AiEmailSuggestThrottle;
use Sfolador\AiEmailSuggest\Services\AiService;
use Sfolador\AiEmailSuggest\Services\AiServiceInterface;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AiEmailSuggestServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('ai-email-suggest')
            ->hasRoute('ai_email_suggest_routes')
            ->hasViews()
            ->hasCommand(AiSuggestClearCache::class)
            ->hasConfigFile();
    }

    public function registeringPackage(): void
    {
        $this->app->bind(AiServiceInterface::class, function () {
            $apiKey = config('ai-email-suggest.openai_key') ?? '';
            /** @phpstan-ignore-next-line  */
            $client = OpenAI::client($apiKey);

            return new AiService($client);
        });

        $this->app->bind(AiEmailSuggestInterface::class, function () {
            /**
             * @var AiServiceInterface $aiEmailSuggestInterface
             */
            $aiEmailSuggestInterface = app(AiServiceInterface::class);

            return new AiEmailSuggest($aiEmailSuggestInterface);
        });

        app('router')->aliasMiddleware('ai-suggest-throttle', AiEmailSuggestThrottle::class);
    }
}
