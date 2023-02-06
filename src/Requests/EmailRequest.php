<?php

namespace Sfolador\AiEmailSuggest\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
