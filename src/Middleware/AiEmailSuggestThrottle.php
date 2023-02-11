<?php

namespace Sfolador\AiEmailSuggest\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpFoundation\Response;

class AiEmailSuggestThrottle extends ThrottleRequests
{
    /** @noinspection CallableParameterUseCaseInTypeContextInspection */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = ''): Response
    {
        if (! config('ai-email-suggest.throttle.enabled')) {
            return $next($request);
        }

        $maxAttempts = config('ai-email-suggest.throttle.max_attempts', $maxAttempts);
        $decayMinutes = config('ai-email-suggest.throttle.decay_minutes', $decayMinutes);
        $prefix = config('ai-email-suggest.throttle.prefix', $prefix);

        /** @phpstan-ignore-next-line  */
        return parent::handle($request, $next, $maxAttempts, $decayMinutes, $prefix);
    }
}
