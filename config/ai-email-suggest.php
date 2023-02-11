<?php

// config for Sfolador/AiEmailSuggest
return [
    'model' => 'text-davinci-003',
    'openai_key' => env('OPENAI_KEY'),
    'default_route' => 'ai-email-suggest',
    'use_cache' => true,
    'throttle' => [
        'enabled' => false,
        'max_attempts' => 60,
        'prefix' => 'ai-email-suggest',
    ],
];
