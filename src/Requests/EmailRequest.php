<?php

namespace Sfolador\AiEmailSuggest\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailRequest extends FormRequest
{
    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
