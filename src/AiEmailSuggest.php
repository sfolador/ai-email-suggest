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

    private function retrieveSuggestion(): string|null
    {
        $response = $this->aiService->getSuggestion($this->createPrompt($this->email));

        $suggestedDomain = $this->extractFirstChoice($response);
        if ($suggestedDomain === '') {
            return $this->suggestion;
        }

        $address = $this->extractEmailAddress($this->email);

        $this->saveSuggestion($this->email, $suggestedDomain);
        $this->suggestion = Str::of($address)->append('@')->append($suggestedDomain)->value();

        return $this->suggestion;
    }

    private function extractFirstChoice(CreateResponse|\OpenAI\Responses\Chat\CreateResponse|null $response): string
    {
        if (! $response) {
            return '';
        }
        if ($response instanceof \OpenAI\Responses\Chat\CreateResponse) {
            if (collect($response->choices)->first()?->message->content === '' || ! collect($response->choices)->first()) {
                return '';
            }

            return Str::of($response->choices[0]->message->content)->trim()->lower()->value();
        }
        if (collect($response->choices)->first()?->text === '' || ! collect($response->choices)->first()) {
            return '';
        }

        return Str::of(collect($response->choices)->first()->text)->trim()->lower()->value();
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

        if ($this->suggestionAlreadySeen($this->email)) {
            $suggestedDomain = $this->cachedSuggestionFor($this->email);
            $address = $this->extractEmailAddress($this->email);
            /** @phpstan-ignore-next-line  */
            $this->suggestion = Str::of($address)->append('@')->append($suggestedDomain)->value();

            return $this->suggestion;
        }

        return $this->retrieveSuggestion();
    }

    public function suggestionAlreadySeen(string $email): bool
    {
        if (! config('ai-email-suggest.use_cache')) {
            return false;
        }

        if (Cache::supportsTags()) {
            return Cache::tags('ai-email-suggest')->has($this->getCacheKey($this->extractDomain($email)));
        }

        return Cache::has($this->getCacheKey($this->extractDomain($email)));
    }

    public function saveSuggestion(string $email, string $suggestion): void
    {
        if (config('ai-email-suggest.use_cache')) {
            if (Cache::supportsTags()) {
                Cache::tags('ai-email-suggest')->forever($this->getCacheKey($this->extractDomain($email)), $suggestion);

                return;
            }
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
