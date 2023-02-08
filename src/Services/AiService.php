<?php

namespace Sfolador\AiEmailSuggest\Services;

use OpenAI;
use OpenAI\Responses\Completions\CreateResponse;

class AiService implements AiServiceInterface
{
    public function __construct(private readonly OpenAI\Client $client)
    {
    }

    public function getSuggestion(string $prompt): CreateResponse
    {
        return  $this->client->completions()->create([
            'prompt' => $prompt,
            'model' => config('ai-email-suggest.model'),
        ]);
    }
}
