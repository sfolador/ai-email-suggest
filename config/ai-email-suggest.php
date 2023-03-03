<?php

// config for Sfolador/AiEmailSuggest
return [
    'model' => 'text-davinci-003',
    'openai_key' => env('OPENAI_KEY'),
    'default_route' => 'ai-email-suggest',
    'use_cache' => true,
    // If you want to use the chatgpt API,
    // you need to set the following value to true:
    'use_chatgpt_api' => true,
    'throttle' => [
        'enabled' => false,
        'max_attempts' => 60,
        'prefix' => 'ai-email-suggest',
    ],
];
