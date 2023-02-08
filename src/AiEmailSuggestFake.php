<?php

namespace Sfolador\AiEmailSuggest;

class AiEmailSuggestFake implements AiEmailSuggestInterface
{
    public function suggest(string $email): string|null
    {
        return $email;
    }

    public function createPrompt(string $email): string
    {
        return view('ai-email-suggest::prompt', [
            'email' => $email,
        ])->render();
    }
}
