<?php

use function Pest\Laravel\post;
use Sfolador\AiEmailSuggest\Facades\AiEmailSuggest;

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

    //  $results = 'test@yahoo.com';

    AiEmailSuggest::fake();

    $response = post(route('ai-email-suggest'), ['email' => $initialInput])->assertOk();

    $this->expect($response->json('suggestion'))->toBe($initialInput);
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
