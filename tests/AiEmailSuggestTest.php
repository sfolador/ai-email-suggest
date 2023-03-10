<?php

use Illuminate\Support\Facades\Cache;
use OpenAI\Responses\Completions\CreateResponse;
use Sfolador\AiEmailSuggest\Facades\AiEmailSuggest;
use Sfolador\AiEmailSuggest\Facades\AiService;
use Sfolador\AiEmailSuggest\Services\AiServiceFake;
use Sfolador\AiEmailSuggest\Services\AiServiceInterface;

beforeEach(function () {
    $this->inputEmail = 'text@example.com';
    config()->set('ai-email-suggest.openai_key', 'test_api_key');

    config()->set('ai-email-suggest.use_cache', true);
});

it('can suggest an email', function () {
    AiEmailSuggest::fake();
    $suggestion = AiEmailSuggest::suggest('email@example.com');
    $this->expect($suggestion)->toBe('email@example.com');
});

it('should suggest a correct email address', function () {
    $initialInput = 'test@yaohh.com';
    $suggestion = 'test@yahoo.com';
    AiEmailSuggest::shouldReceive('suggest')->andReturn($suggestion);

    $results = AiEmailSuggest::suggest($initialInput);
    $this->expect($results)->toBe($suggestion);
});

it('can use cache to avoid api calls', function () {
    $inputEmail = 'text@exampl.com';
    $cacheKey = 'ai-email-suggest-'.'exampl.com';

    $results = 'text@example.com';

    Cache::shouldReceive('supportsTags')->andReturn(false);
    Cache::shouldReceive('has')->once()->with($cacheKey)->andReturn(true);
    Cache::shouldReceive('get')->once()->with($cacheKey)->andReturn('example.com');

    $suggestion = AiEmailSuggest::suggest($inputEmail);

    $this->expect($suggestion)->toBe($results);
});

it('saves suggestions in cache', function () {
    $inputEmail = 'text@example.com';
    $cacheKey = 'ai-email-suggest-'.'example.com';
    Cache::shouldReceive('supportsTags')->andReturn(false);
    Cache::shouldReceive('forever')->once()->withArgs([$cacheKey, 'suggestion']);
    AiEmailSuggest::saveSuggestion($inputEmail, 'suggestion');
});

it('saves suggestions in cache with tags', function () {
    $inputEmail = 'text@example.com';
    $cacheKey = 'ai-email-suggest-'.'example.com';
    Cache::shouldReceive('supportsTags')->andReturn(true);
    Cache::shouldReceive('tags')->with('ai-email-suggest')->andReturnSelf();
    Cache::shouldReceive('forever')->once()->withArgs([$cacheKey, 'suggestion']);
    AiEmailSuggest::saveSuggestion($inputEmail, 'suggestion');
});

it('checks if suggestion is already seen', function () {
    $inputEmail = 'text@example.com';
    $cacheKey = 'ai-email-suggest-'.'example.com';

    Cache::shouldReceive('supportsTags')->andReturn(false);
    Cache::shouldReceive('has')->once()->with($cacheKey)->andReturn(true);

    expect(AiEmailSuggest::suggestionAlreadySeen($inputEmail))->toBeTrue();
});

it('checks if suggestion is already seen with tags enabled', function () {
    $inputEmail = 'text@example.com';
    $cacheKey = 'ai-email-suggest-'.'example.com';

    Cache::shouldReceive('supportsTags')->andReturn(true);
    Cache::shouldReceive('tags')->with('ai-email-suggest')->andReturnSelf();
    Cache::shouldReceive('has')->once()->with($cacheKey)->andReturn(true);

    expect(AiEmailSuggest::suggestionAlreadySeen($inputEmail))->toBeTrue();
});

it('suggestion has not been seen if the config is false', function () {
    $inputEmail = 'text@example.com';

    config()->set('ai-email-suggest.use_cache', false);

    expect(AiEmailSuggest::suggestionAlreadySeen($inputEmail))->toBeFalse();
});

it('suggestion has not been seen if the config is false with tags enabled', function () {
    $inputEmail = 'text@example.com';

    config()->set('ai-email-suggest.use_cache', false);
    Cache::shouldReceive('supportsTags')->andReturn(true);
    Cache::shouldReceive('tags')->with('ai-email-suggest')->andReturnSelf();
    expect(AiEmailSuggest::suggestionAlreadySeen($inputEmail))->toBeFalse();
});

it('returns a null suggestion if api response is null', function () {
    $inputEmail = 'text@example.com';

    $prompt = view('ai-email-suggest::prompt', ['email' => $inputEmail])->render();

    //AiService::fake();
    AiService::shouldReceive('getSuggestion')
        ->withArgs([$prompt])
        ->andReturnNull();

    $results = AiEmailSuggest::suggest($inputEmail);
    expect($results)->toBeNull();
});

it('returns a null suggestion if api text is empty', function () {
    $inputEmail = fake()->email;

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

    //AiService::fake();
    AiService::shouldReceive('getSuggestion')
        ->withArgs([$prompt])
        ->andReturn($response);

    //  AiEmailSuggest::shouldReceive('suggestionAlreadySeen')->andReturnFalse();

    $results = AiEmailSuggest::suggest($inputEmail);
    expect($results)->toBeNull();
});

it('returns a null suggestion', function () {
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

it('can create a prompt', function () {
    $inputEmail = 'text@example.com';

    $prompt = AiEmailSuggest::createPrompt($inputEmail);

    expect($prompt)
        ->toContain(Str::of('Input:')->append($inputEmail)->value());
});

it('can create a prompt with the fake facade', function () {
    $inputEmail = 'text@example.com';

    AiEmailSuggest::fake();
    $prompt = AiEmailSuggest::createPrompt($inputEmail);

    expect($prompt)
        ->toContain(Str::of('Input:')->append($inputEmail)->value());
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

it('service returns a createresponse', function () {
    config()->set('ai-email-suggest.use_chatgpt_api', false);
    $client = mockClient('POST', 'completions', [
        'model' => config('ai-email-suggest.model'),
        'prompt' => 'prompt',
    ], [
        'id' => 'cmpl-asd23jkmsdfsdf',
        'object' => 'text_completion',
        'created' => 167812432,
        'model' => 'text-davinci-003',
        'choices' => [
            [
                'text' => 'text in response',
                'index' => 0,
                'logprobs' => null,
                'finish_reason' => 'length',
            ],
        ],
        'usage' => [
            'prompt_tokens' => 1,
            'completion_tokens' => 2,
            'total_tokens' => 3,
        ],
    ]);

    $service = new \Sfolador\AiEmailSuggest\Services\AiService($client);

    expect($service->getSuggestion('prompt'))->toBeInstanceOf(CreateResponse::class);
});

//
//it('service returns a chat create response', function () {
//    config()->set('ai-email-suggest.use_chatgpt_api',true);
//    $client = mockClient('POST', 'chat/completions', [
//        'model' => 'gpt-3.5-turbo',
//        'messages' => [['role' => 'user','content' => 'prompt']],
//    ], [
//        'id' => 'cmpl-asd23jkmsdfsdf',
//        'object' => 'text_completion',
//        'created' => 167812432,
//        'model' => 'gpt-3.5-turbo',
//        'choices' => [
//            [
//                'message' => [
//                    "role" => "assistant",
//                    "content" => "text in response",
//                ]
//            ],
//        ],
//        'usage' => [
//            'prompt_tokens' => 1,
//            'completion_tokens' => 2,
//            'total_tokens' => 3,
//        ],
//    ]);
//
//    $service = new \Sfolador\AiEmailSuggest\Services\AiService($client);
//
//    expect($service->getSuggestion('prompt'))->toBeInstanceOf(\OpenAI\Responses\Chat\CreateResponse::class);
//});
