<?php

namespace Sfolador\AiEmailSuggest\Commands;

use Illuminate\Console\Command;

class AiEmailSuggestCommand extends Command
{
    public $signature = 'ai-email-suggest';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
