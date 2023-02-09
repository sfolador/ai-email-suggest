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
