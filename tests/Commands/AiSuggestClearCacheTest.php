<?php

it('does not do anything if cache does not support tags', function () {
    Cache::shouldReceive('supportsTags')->andReturn(false);

    $this->artisan('email-suggest:clear')
        ->expectsOutput('The cache driver does not support tags')
        ->assertExitCode(0);
});

it('clears cache id cache supports tags', function () {
    Cache::shouldReceive('supportsTags')->andReturn(true);

    Cache::shouldReceive('tags')->with('ai-email-suggest')->andReturnSelf();
    Cache::shouldReceive('flush');

    $this->artisan('email-suggest:clear')
        ->expectsQuestion('Are you sure you want to clear the cache of the email suggestions? (y/n)', 'y')
        ->expectsOutput('Clearing the cache of the email suggestions')
        ->expectsOutput('AiSuggestEmail Cache cleared!')
        ->assertExitCode(0);
});

it('does not clear cache if user does not want to', function () {
    Cache::shouldReceive('supportsTags')->andReturn(true);

    $this->artisan('email-suggest:clear')
        ->expectsQuestion('Are you sure you want to clear the cache of the email suggestions? (y/n)', 'n')
        ->doesntExpectOutput('Clearing the cache of the email suggestions')
        ->doesntExpectOutput('AiSuggestEmail Cache cleared!')
        ->assertExitCode(0);
});
