<?php

/**
 * @see https://github.com/openai-php/client
 *
 * @return array<string, mixed>
 */
function completion(): array
{
    return [
        'id' => 'cmpl-asd23jkmsdfsdf',
        'object' => 'text_completion',
        'created' => 167812432,
        'model' => 'text-davinci-003',
        'choices' => [
            [
                'text' => 'text in response',
                'index' => 0,
                'logprobs' => null,
                'finish_reason' => 'length',
            ],
        ],
        'usage' => [
            'prompt_tokens' => 1,
            'completion_tokens' => 2,
            'total_tokens' => 3,
        ],
    ];
}
