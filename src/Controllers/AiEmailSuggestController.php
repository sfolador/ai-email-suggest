<?php

namespace Sfolador\AiEmailSuggest\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Sfolador\AiEmailSuggest\Facades\AiEmailSuggest;

class AiEmailSuggestController extends Controller
{
    public function suggest(Request $request)
    {
        $email = $request->get('email');
        $suggestion = AiEmailSuggest::suggest($email);

        return response()->json([
            'suggestion' => $suggestion
        ]);
    }
}
