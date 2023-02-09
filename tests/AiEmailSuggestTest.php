<?php

use Illuminate\Support\Facades\Cache;
use OpenAI\Responses\Completions\CreateResponse;
use function Pest\Laravel\post;
use Sfolador\AiEmailSuggest\Facades\AiEmailSuggest;
use Sfolador\AiEmailSuggest\Facades\AiService;
use Sfolador\AiEmailSuggest\Services\AiServiceFake;
use Sfolador\AiEmailSuggest\Services\AiServiceInterface;

beforeEach(function () {
    $this->inputEmail = 'text@example.com';
    config()->set('ai-email-suggest.openai_key', 'test_api_key');
});

it('can suggest an email', function () {
    AiEmailSuggest::fake();
    $suggestion = AiEmailSuggest::suggest('email@example.com');
    $this->expect($suggestion)->toBe('email@example.com');
});

it('should suggest a correct email address', function () {
    $initialInput = 'test@yaohh.com';

    $mocked = \Pest\Laravel\mock(\Sfolador\AiEmailSuggest\AiEmailSuggest::class)
        ->shouldReceive('suggest')
        ->withArgs([$initialInput])
        ->andReturn('test@yahoo.com')->getMock();

    $results = $mocked->suggest($initialInput);
    $this->expect($results)->toBe('test@yahoo.com');
});

it('should return a suggestion from a controller', function () {
    $initialInput = 'test@yaoh.com';

    AiEmailSuggest::fake();

    $response = post(route('ai-email-suggest'), ['email' => $initialInput])->assertOk();

    expect($response->json('suggestion'))->toBe($initialInput);
});

it('validates the email address', function () {
    $initialInput = 'wrong_email@';

    AiEmailSuggest::fake();

    post(route('ai-email-suggest'), ['email' => $initialInput])->assertInvalid(['email']);
    post(route('ai-email-suggest'), ['email' => null])->assertInvalid(['email' => 'required']);
});

it('checks if email address is null', function () {
    AiEmailSuggest::fake();
    post(route('ai-email-suggest'), ['email' => null])->assertInvalid(['email' => 'required']);
});

it('can use cache to avoid api calls', function () {
    $inputEmail = 'text@example.com';
    $cacheKey = 'ai-email-suggest-'.'example.com';

    Cache::shouldReceive('has')->once()->with($cacheKey)->andReturn(true);
    Cache::shouldReceive('get')->once()->with($cacheKey)->andReturn('example.com');

    $suggestion = AiEmailSuggest::suggest($inputEmail);

    $this->expect($suggestion)->toBe('text@example.com');
});

it('saves suggestions in cache', function () {
    $inputEmail = 'text@example.com';
    $cacheKey = 'ai-email-suggest-'.'example.com';

    Cache::shouldReceive('forever')->once()->withArgs([$cacheKey, 'suggestion']);
    AiEmailSuggest::saveSuggestion($inputEmail, 'suggestion');
});

it('checks if suggestion is already seen', function () {
    $inputEmail = 'text@example.com';
    $cacheKey = 'ai-email-suggest-'.'example.com';

    Cache::shouldReceive('has')->once()->with($cacheKey)->andReturn(true);

    expect(AiEmailSuggest::suggestionAlreadySeen($inputEmail))->toBeTrue();
});

it('suggestion has not been seen if the config is false', function () {
    $inputEmail = 'text@example.com';

    config()->set('ai-email-suggest.use_cache', false);

    expect(AiEmailSuggest::suggestionAlreadySeen($inputEmail))->toBeFalse();
});

it('could return a null suggestion', function () {
    $inputEmail = 'text@example.com';

    $response = CreateResponse::from(
        [
            'id' => '1',
            'object' => 'text_completion',
            'created' => 1,
            'model' => 'davinci:2020-05-03',
            'choices' => [
                [
                    'text' => '',
                    'index' => 1,
                    'logprobs' => null,
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 1,
                'completion_tokens' => 1,
                'total_tokens' => 1,
            ],
        ]
    );

    $prompt = view('ai-email-suggest::prompt', ['email' => $inputEmail])->render();

    AiService::fake();
    AiService::shouldReceive('getSuggestion')
        ->withArgs([$prompt])
        ->andReturn($response);

    $results = AiEmailSuggest::suggest($inputEmail);
    expect($results)->toBeNull();
});

it('could return a empty suggestion', function () {
    $inputEmail = 'text@example.com';

    $response = null;

    $prompt = view('ai-email-suggest::prompt', ['email' => $inputEmail])->render();

    AiService::fake();
    AiService::shouldReceive('getSuggestion')
        ->withArgs([$prompt])
        ->andReturn($response);

    $results = AiEmailSuggest::suggest($inputEmail);
    expect($results)->toBeNull();
});

it('creates a prompt with a view', function () {
    $inputEmail = 'text@example.com';

    $prompt = AiEmailSuggest::createPrompt($inputEmail);

    expect($prompt)->toContain($inputEmail);
});

it('can create a prompt', function () {
    AiEmailSuggest::fake();

    expect(AiEmailSuggest::createPrompt($this->inputEmail))->toContain($this->inputEmail);
});

it('returns a response', function () {
    AiService::fake();
    $randName = Str::random(10);
    $randEmail = $randName.'@gmial.com';
    $response = AiEmailSuggest::suggest($randEmail);

    expect($response)
        ->toBeString()
        ->toBe($randName.'@test.com');
});

it('has a fake version of the service', function () {
    $service = app(AiServiceInterface::class);
    expect($service)->toBeInstanceOf(\Sfolador\AiEmailSuggest\Services\AiService::class);

    AiService::fake();
    $service = app(AiServiceInterface::class);

    expect($service)->toBeInstanceOf(AiServiceFake::class);
});
