<?php

namespace Sfolador\AiEmailSuggest\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class AiSuggestClearCache extends Command
{
    protected $signature = 'email-suggest:clear';

    protected $description = 'Clears the cache of the email suggestions';

    public function handle(): void
    {
        if (! Cache::supportsTags()) {
            $this->error('The cache driver does not support tags');

            return;
        }

        $answer = $this->ask('Are you sure you want to clear the cache of the email suggestions? (y/n)', 'n');

        if ($answer !== 'y') {
            $this->info('Ok, bye!');

            return;
        }

        $this->info('Clearing the cache of the email suggestions');
        Cache::tags('ai-email-suggest')->flush();

        $this->info('AiSuggestEmail Cache cleared!');
    }
}
