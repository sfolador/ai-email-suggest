<?php

// config for Sfolador/AiEmailSuggest
return [
    'prompt' => 'The input is: %input%.  Assume that the input domain has been misspelled and it must be corrected. Which most popular email domains is similar to the input domain?
       Give only the domain as a result.',
    'model' => 'text-davinci-003',
    'openai_key' => env('OPENAI_KEY'),
    'default_response' => 'Maybe you meant %suggestion%?',
];
