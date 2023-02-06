<?php

use DG\BypassFinals;
use Illuminate\Support\Facades\Cache;
use OpenAI\Responses\Completions\CreateResponse;
use function Pest\Laravel\post;
use Sfolador\AiEmailSuggest\Facades\AiEmailSuggest;

beforeEach(function () {
    BypassFinals::enable();
    $inputEmail = 'text@example.com';
    config()->set('ai-email-suggest.openai_key', 'test_api_key');
});

it('can suggest an email', function () {
    AiEmailSuggest::fake();

    $suggestion = AiEmailSuggest::suggest('email@example.com');
    $this->expect($suggestion)->toBe('email@example.com');
});

it('should suggest a correct email address', function () {
    $initialInput = 'test@yaohh.com';
    config()->set('ai-email-suggest.openai_key', 'test_api_key');

    $mocked = \Pest\Laravel\mock(\Sfolador\AiEmailSuggest\AiEmailSuggest::class)
        ->shouldReceive('suggest')
        ->withArgs([$initialInput])
        ->andReturn('test@yahoo.com')->getMock();

    $results = $mocked->suggest($initialInput);
    $this->expect($results)->toBe('test@yahoo.com');
});

it('should return a suggestion from a controller', function () {
    $initialInput = 'test@yaohh.com';

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

it('returns an empty prompt if fake', function () {
    $inputEmail = 'text@example.com';
    AiEmailSuggest::fake();
    expect(AiEmailSuggest::createPrompt($inputEmail))->toBe('');
});

it('can use cache', function () {
    $inputEmail = 'text@example.com';
    $cacheKey = 'ai-email-suggest-'.$inputEmail;

    Cache::shouldReceive('has')->once()->with($cacheKey)->andReturn(true);
    Cache::shouldReceive('get')->once()->with($cacheKey)->andReturn('text@text.com');

    config()->set('ai-email-suggest.openai_key', 'test_api_key');

    $suggestion = AiEmailSuggest::suggest($inputEmail);

    $this->expect($suggestion)->toBe('text@text.com');
});

it('saves suggestions in cache', function () {
    $inputEmail = 'text@example.com';
    $cacheKey = 'ai-email-suggest-'.$inputEmail;

    config()->set('ai-email-suggest.openai_key', 'test_api_key');

    Cache::shouldReceive('forever')->once()->withArgs([$cacheKey, 'suggestion']);

    $aiSuggest = new \Sfolador\AiEmailSuggest\AiEmailSuggest();
    $aiSuggest->saveSuggestion($inputEmail, 'suggestion');
});

it('checks if suggestion is already seen', function () {
    $inputEmail = 'text@example.com';
    $cacheKey = 'ai-email-suggest-'.$inputEmail;

    config()->set('ai-email-suggest.openai_key', 'test_api_key');
    Cache::shouldReceive('has')->once()->with($cacheKey)->andReturn(true);

    $aiSuggest = new \Sfolador\AiEmailSuggest\AiEmailSuggest();
    expect($aiSuggest->suggestionAlreadySeen($inputEmail))->toBeTrue();
});

it('suggestion has not been seen if the config is false', function () {
    $inputEmail = 'text@example.com';

    config()->set('ai-email-suggest.openai_key', 'test_api_key');
    config()->set('ai-email-suggest.use_cache', false);

    $aiSuggest = new \Sfolador\AiEmailSuggest\AiEmailSuggest();
    expect($aiSuggest->suggestionAlreadySeen($inputEmail))->toBeFalse();
});

it('could return a null suggestion', function () {
    $inputEmail = 'text@example.com';
    config()->set('ai-email-suggest.openai_key', 'test_api_key');

    $mocked = \Pest\Laravel\mock(\Sfolador\AiEmailSuggest\AiEmailSuggest::class)
        ->makePartial()
        ->shouldReceive('suggestionAlreadySeen')
        ->withArgs([$inputEmail])
        ->andReturn($inputEmail)->getMock();

    $mocked->shouldReceive('hasSuggestion')
        ->andReturnFalse()->getMock();

    $results = $mocked->suggest($inputEmail);
    expect($results)->toBeNull();
});

it('creates a prompt with a view', function () {
    $inputEmail = 'text@example.com';

    $aiSuggest = new Sfolador\AiEmailSuggest\AiEmailSuggest();
    $prompt = $aiSuggest->createPrompt($inputEmail);

    expect($prompt)->toContain($inputEmail);
});

//
//it('get response api',function(){
//    $inputEmail = "text@example.com";
//
//    $aiSuggest = new Sfolador\AiEmailSuggest\AiEmailSuggest();
//
//    $prompt = $aiSuggest->createPrompt($inputEmail);
//    $createParams = [
//        'prompt' => $prompt,
//        'model' => config('ai-email-suggest.model'),
//    ];
//
//    $response = CreateResponse::from([
//        'id' => 'test',
//        'object' => 'test',
//        'created' => 1,
//        'model' => 'test',
//        'choices' => [
//            [
//                'text' =>    $inputEmail ,
//                'index' => 1,
//                'logprobs' => null,
//                'finish_reason' => 'test',
//            ]
//        ],
//        'usage' => [         'prompt_tokens' =>   1  ,
//            'completion_tokens' => 1,
//            'total_tokens' => 2
//            ]
//    ]);
//
//    $mocked = \Pest\Laravel\mock(\OpenAI\Resources\Completions::class)
//        ->makePartial()
//        ->shouldReceive('create')
//        ->withArgs([$createParams])
//        ->andReturn($response)->getMock();
//
//    $aiSuggest->suggest($inputEmail);
//    expect($aiSuggest->getApiResponse())->toBe($response);
//
//
//});
