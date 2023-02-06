<?php

namespace Sfolador\AiEmailSuggest\Controllers;

use Illuminate\Routing\Controller;
use Sfolador\AiEmailSuggest\Facades\AiEmailSuggest;
use Sfolador\AiEmailSuggest\Requests\EmailRequest;

class AiEmailSuggestController extends Controller
{
    public function suggest(EmailRequest $request)
    {
        $email = $request->get('email');

        $suggestion = AiEmailSuggest::suggest($email);

        return response()->json([
            'suggestion' => $suggestion,
        ]);
    }
}
