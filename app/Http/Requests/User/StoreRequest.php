<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
            'name'  => ['required','string','max:254'],
            'email' => ['required', 'email', 'max:254', 'unique:users'],
            'password' => ['required', 'string', 'max:254', 'confirmed'],
            'pin_code' => ['required', 'digits:4'],
            'role_id'  => ['required', Rule::in([1, 2, 3])],
        ];
    }
}
