<?php


use Illuminate\Support\Facades\Route;
use Sfolador\AiEmailSuggest\Controllers\AiEmailSuggestController;

Route::post('/ai-email-suggest', [AiEmailSuggestController::class, 'suggest'])->name('ai-email-suggest');
