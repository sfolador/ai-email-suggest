<?php

namespace Sfolador\AiEmailSuggest\Services;

use OpenAI;
use OpenAI\Responses\Completions\CreateResponse;

class AiService implements AiServiceInterface
{
    public function __construct(private readonly OpenAI\Client $client)
    {
    }

    public function getSuggestion(string $prompt): CreateResponse|OpenAI\Responses\Chat\CreateResponse|null
    {
        if (config('ai-email-suggest.use_chatgpt_api')) {
            return  $this->client->chat()->create([

                'messages' => [['role' => 'user', 'content' => $prompt]],
                'model' => 'gpt-3.5-turbo',
            ]);
        }

        return  $this->client->completions()->create([
            'prompt' => $prompt,
            'model' => config('ai-email-suggest.model'),
        ]);
    }
}
