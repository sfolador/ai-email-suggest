<?php

namespace Sfolador\AiEmailSuggest;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use OpenAI\Responses\Completions\CreateResponse;
use Sfolador\AiEmailSuggest\Services\AiServiceInterface;

class AiEmailSuggest implements AiEmailSuggestInterface
{
    private string|null $suggestion = null;

    private string $email;

    public function __construct(private readonly AiServiceInterface $aiService)
    {
    }

    private function retrieveSuggestion(): void
    {
        if ($this->suggestionAlreadySeen($this->email)) {
            $suggestedDomain = $this->cachedSuggestionFor($this->email);
            $address = $this->extractEmailAddress($this->email);
            $this->suggestion = Str::of($address)->append('@')->append($suggestedDomain)->value();

            return;
        }

        $response = $this->aiService->getSuggestion($this->createPrompt($this->email));

        $suggestedDomain = $this->extractFirstChoice($response);
        if ($suggestedDomain === '') {
            $this->suggestion = null;

            return;
        }

        $address = $this->extractEmailAddress($this->email);

        $this->suggestion = Str::of($address)->append('@')->append($suggestedDomain)->value();
        $this->saveSuggestion($this->email, $suggestedDomain);
    }

    private function extractFirstChoice(?CreateResponse $response): string
    {
        if (! $response) {
            return '';
        }

        return Str::of(collect($response->choices)->first()?->text)->trim()->value();
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
        $this->retrieveSuggestion();

        return $this->suggestion;
    }

    public function suggestionAlreadySeen(string $email): bool
    {
        if (! config('ai-email-suggest.use_cache')) {
            return false;
        }

        return Cache::has($this->getCacheKey($this->extractDomain($email)));
    }

    public function saveSuggestion(string $email, string $suggestion): void
    {
        if (config('ai-email-suggest.use_cache')) {
            Cache::forever($this->getCacheKey($this->extractDomain($email)), $suggestion);
        }
    }

    private function cachedSuggestionFor(string $email): mixed
    {
        return Cache::get($this->getCacheKey($this->extractDomain($email)));
    }

    private function getCacheKey(string $email): string
    {
        return 'ai-email-suggest-'.$this->extractDomain($email);
    }

    private function extractDomain(string $email): string
    {
        return Str::of($email)->after('@')->value();
    }

    private function extractEmailAddress(string $email): string
    {
        return Str::of($email)->before('@')->value();
    }
}
