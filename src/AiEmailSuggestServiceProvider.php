<?php

namespace Sfolador\AiEmailSuggest;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Sfolador\AiEmailSuggest\Commands\AiEmailSuggestCommand;

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
            ->hasConfigFile();
    }

    public function registeringPackage()
    {

       // $this->app->alias(AiEmailSuggest::class, 'email-suggest');

        $this->app->bind(AiEmailSuggestInterface::class, function () {
            return new AiEmailSuggest();
        });
    }
}
