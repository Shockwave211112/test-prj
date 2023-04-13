<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
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
            'number' => ['unique:orders', 'string'],
            'total_cost' => ['digits_between:1,7'],
            'user_id' => ['exists:users,id'],
            'closed_at' => ['date'],
            'is_closed' => ['boolean']
        ];
    }
}
