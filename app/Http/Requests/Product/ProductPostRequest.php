<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ProductPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|unique:products',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:hot dish,cold dish,drink,dessert',
            'photo' => 'required|image|max:8192',
        ];
    }
}
