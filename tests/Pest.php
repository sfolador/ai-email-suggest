<?php

use OpenAI\Client;
use OpenAI\Contracts\Transporter;
use OpenAI\ValueObjects\ApiKey;
use OpenAI\ValueObjects\Transporter\BaseUri;
use OpenAI\ValueObjects\Transporter\Headers;
use OpenAI\ValueObjects\Transporter\Payload;
use Sfolador\AiEmailSuggest\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * @see https://github.com/openai-php/client/blob/main/tests/Pest.php
 *
 * @return Client
 */
function mockClient(string $method, string $resource, array $params, array|string $response, $methodName = 'requestObject')
{
    $transporter = Mockery::mock(Transporter::class);

    $transporter
        ->shouldReceive($methodName)
        ->once()
        ->withArgs(function (Payload $payload) use ($method, $resource) {
            $baseUri = BaseUri::from('api.openai.com/v1');
            $headers = Headers::withAuthorization(ApiKey::from('foo'));

            $request = $payload->toRequest($baseUri, $headers);

            return $request->getMethod() === $method
                && $request->getUri()->getPath() === "/v1/$resource";
        })->andReturn($response);

    return new Client($transporter);
}
