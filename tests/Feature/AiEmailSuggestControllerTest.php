<?php

use function Pest\Laravel\post;
use Sfolador\AiEmailSuggest\Facades\AiEmailSuggest;

it('should return a suggestion', function () {
    $initialInput = 'test@yaoh.com';
    AiEmailSuggest::fake();

    $response = post(route('ai-email-suggest'), ['email' => $initialInput])->assertOk();

    expect($response->json('suggestion'))
        ->toBe($initialInput);
});

it('validates the email address', function () {
    $initialInput = 'wrong_email@';

    AiEmailSuggest::fake();

    post(route('ai-email-suggest'), ['email' => $initialInput])->assertInvalid(['email']);
    post(route('ai-email-suggest'), ['email' => null])
        ->assertInvalid(['email' => 'required']);
});

it('requires an email address', function () {
    AiEmailSuggest::fake();
    post(route('ai-email-suggest'), ['email' => null])
        ->assertInvalid(['email' => 'required']);
});

it('should not throttle requests if throttle is disabled', function () {
    config()->set('ai-email-suggest.throttle.enabled', false);
    $initialInput = 'test@yaoh.com';
    AiEmailSuggest::fake();

    post(route('ai-email-suggest'), ['email' => $initialInput])->assertOk();
});

it('should throttle requests', function () {
    config()->set('ai-email-suggest.throttle.enabled', true);
    config()->set('ai-email-suggest.throttle.max_attempts', 2);
    $initialInput = 'test@yaoh.com';
    AiEmailSuggest::fake();

    post(route('ai-email-suggest'), ['email' => $initialInput])->assertOk();
    post(route('ai-email-suggest'), ['email' => $initialInput])->assertOk();
    post(route('ai-email-suggest'), ['email' => $initialInput])->assertTooManyRequests();
});
