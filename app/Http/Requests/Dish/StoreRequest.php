<?php

namespace App\Http\Requests\Dish;

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
            'category_id' => ['required', 'digits_between:1,3', 'exists:categories,id'],
            'name'  => ['unique:dishes', 'required','string'],
            'img' => ['required', 'image:jpg,jpeg,png'],
            'calories' => ['required', 'digits_between:1,5'],
            'price' => ['required', 'digits_between:1,5'],
            'composition' => ['required', 'string']
        ];
    }
}
