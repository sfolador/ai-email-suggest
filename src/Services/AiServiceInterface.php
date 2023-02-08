<?php

namespace Sfolador\AiEmailSuggest\Services;

use OpenAI\Responses\Completions\CreateResponse;

interface AiServiceInterface
{
    public function getSuggestion(string $prompt): ?CreateResponse;
}
