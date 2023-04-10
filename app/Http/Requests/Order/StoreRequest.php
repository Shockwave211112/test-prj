<?php

namespace App\Http\Requests\Order;

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
            'number' => ['required', 'unique:orders', 'string'],
            'count'  => ['digits_between:1,3'],
            'total_cost' => ['digits_between:1,7'],
            'user_id' => ['required', 'exists:users,id'],
            'closing_date' => ['date']
        ];
    }
}