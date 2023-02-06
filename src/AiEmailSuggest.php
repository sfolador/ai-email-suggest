<?php

namespace Sfolador\AiEmailSuggest;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use OpenAI;
use OpenAI\Responses\Completions\CreateResponse;

class AiEmailSuggest implements AiEmailSuggestInterface
{
    private OpenAI\Client $client;

    private string|null $suggestion;

    private string $email;

    public function __construct()
    {
        $this->client = OpenAI::client(config('ai-email-suggest.openai_key'));
    }

    private function getSuggestion(): void
    {
        if ($this->suggestionAlreadySeen($this->email)){
            $this->suggestion = $this->getSeenSuggestion($this->email);
            return;
        }
        $response = $this->getApiResponse();

        $this->suggestion = Str::of(collect($response->choices)->first()->text)->trim()->value();
        $this->saveSuggestion($this->email, $this->suggestion);
    }

    public function getApiResponse(): CreateResponse
    {
        return  $this->client->completions()->create([
            'prompt' => $this->createPrompt($this->email),
            'model' => config('ai-email-suggest.model'),
        ]);
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
            return $this->suggestion;
        }

        return null;
    }

    public function hasSuggestion(): bool
    {
        return $this->suggestion !== $this->email;
    }

    public function suggestionAlreadySeen($email): bool
    {
        if (!config('ai-email-suggest.use_cache')) {
            return false;
        }
        return Cache::has($this->getCacheKey($email));
    }

    public function saveSuggestion($email, $suggestion): void
    {
        if (config('ai-email-suggest.use_cache')) {
            Cache::forever($this->getCacheKey($email), $suggestion);
        }
    }

    private function getSeenSuggestion($email): mixed
    {
        return Cache::get($this->getCacheKey($email));
    }

    private function getCacheKey($email): string
    {
        return 'ai-email-suggest-' . $email;
    }
}
