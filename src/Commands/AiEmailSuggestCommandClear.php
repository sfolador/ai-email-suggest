<?php

namespace Sfolador\AiEmailSuggest\Commands;

use Illuminate\Console\Command;

class AiEmailSuggestCommandClear extends Command
{
    protected $signature = 'email-suggest:clear';

    protected $description = 'Clears the cache of the email suggestions';

    public function handle(): void
    {
        $this->info('WIP');
    }
}
