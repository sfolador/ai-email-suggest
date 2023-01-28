<?php

namespace Sfolador\AiEmailSuggest;

use Illuminate\Support\Str;
use OpenAI;

class AiEmailSuggest
{

    public function suggest($email): string
    {
        $client = OpenAI::client(config('ai-email-suggest.openai_key'));

        $response = $client->completions()->create([
            'prompt' => str_replace('%input%', $email, config('ai-email-suggest.prompt')),
            'model' => config('ai-email-suggest.model')
        ]);

        return Str::of(collect($response->choices)->first()->text)->trim()->value();
    }
}
