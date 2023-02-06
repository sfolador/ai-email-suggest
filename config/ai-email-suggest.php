<?php

// config for Sfolador/AiEmailSuggest
return [
    'model' => 'text-davinci-003',
    'openai_key' => env('OPENAI_KEY'),
    'default_response' => 'Maybe you meant %suggestion%?',
    'default_route' => 'ai-email-suggest',
];
