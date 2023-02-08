<?php

namespace Sfolador\AiEmailSuggest;

use OpenAI;
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
            ->hasConfigFile();
    }

    public function registeringPackage()
    {
        // $this->app->alias(AiEmailSuggest::class, 'email-suggest');

        $this->app->bind(AiServiceInterface::class, function () {
            $client = OpenAI::client(config('ai-email-suggest.openai_key'));

            return new AiService($client);
        });

        $this->app->bind(AiEmailSuggestInterface::class, function () {
            return new AiEmailSuggest(app(AiServiceInterface::class));
        });
    }
}
