<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required_without:pin_code', 'email', 'max:254', 'nullable'],
            'password' => ['string', 'max:254', 'nullable', 'required_with:email'],
            'pin_code' => ['digits:4', 'nullable', 'required_without:email'],
        ];
    }
}
