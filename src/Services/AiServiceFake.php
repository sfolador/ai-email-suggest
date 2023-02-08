<?php

namespace Sfolador\AiEmailSuggest\Services;

use OpenAI\Responses\Completions\CreateResponse;

class AiServiceFake implements AiServiceInterface
{
    public function getSuggestion(string $prompt): ?CreateResponse
    {
        return CreateResponse::from(
            [
                'id' => '1',
                'object' => 'text_completion',
                'created' => 1,
                'model' => 'davinci:2020-05-03',
                'choices' => [],
                'usage' => [
                    'prompt_tokens' => 1,
                    'completion_tokens' => 1,
                    'total_tokens' => 1,
                ],
            ]
        );
    }
}
