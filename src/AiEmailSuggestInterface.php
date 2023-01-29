<?php

namespace Sfolador\AiEmailSuggest;

interface AiEmailSuggestInterface
{
    public function suggest(string $email): string|null;

    public function createPrompt(string $email): string;
}
