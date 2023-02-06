<?php

namespace Sfolador\AiEmailSuggest;

use Illuminate\Support\Str;
use OpenAI;

class AiEmailSuggest implements AiEmailSuggestInterface
{
    private OpenAI\Client $client;

    private string|null $suggestion;

    private string $email;

    public function __construct()
    {
        $this->client = OpenAI::client(config('ai-email-suggest.openai_key'));
    }

    private function getSuggestion()
    {
        $response = $this->client->completions()->create([
            'prompt' => $this->createPrompt($this->email),
            'model' => config('ai-email-suggest.model'),
        ]);

        $this->suggestion = Str::of(collect($response->choices)->first()->text)->trim()->value();
    }

    public function createPrompt(string $email): string
    {
        return view('ai-email-suggest::prompt', [
            'email' => $email,
        ])->render();
    }

    public function suggest(string $email): string|null
    {
        $this->email = $email;
        $this->getSuggestion();

        if ($this->hasSuggestion()) {
            return $this->getEmailAddressWithNoDomain().'@'.$this->suggestion;
        }

        return null;
    }

    private function getEmailAddressWithNoDomain(): string
    {
        return explode('@', $this->email)[0];
    }

    public function hasSuggestion(): bool
    {
        if ($this->suggestion === $this->email) {
            return false;
        }

        return true;
    }
}
