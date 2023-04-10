<?php

namespace App\Http\Requests\Dish;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'category_id' => ['digits_between:1,3', 'exists:categories,id'],
            'name'  => ['unique:dishes', 'string'],
            'img' => ['image:jpg,jpeg,png'],
            'calories' => ['digits_between:1,5'],
            'price' => ['digits_between:1,5'],
            'composition' => ['string']
        ];
    }
}
