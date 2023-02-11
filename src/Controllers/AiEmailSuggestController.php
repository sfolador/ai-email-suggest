<?php

namespace Sfolador\AiEmailSuggest\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Sfolador\AiEmailSuggest\Facades\AiEmailSuggest;
use Sfolador\AiEmailSuggest\Requests\EmailRequest;

class AiEmailSuggestController extends Controller
{
    public function suggest(EmailRequest $request): JsonResponse
    {
        $email = $request->get('email');

        /** @phpstan-ignore-next-line  */
        $suggestion = AiEmailSuggest::suggest($email);

        return response()->json([
            'suggestion' => $suggestion,
        ]);
    }
}
