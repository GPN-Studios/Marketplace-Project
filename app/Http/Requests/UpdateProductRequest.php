<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|min:5|max:255',
            'image' => 'sometimes|image|max:2048',
            'description' => 'sometimes|string|max:1000',
            'price' => 'sometimes|numeric|min:1',
            'stock' => 'sometimes|integer|min:1',
        ];
    }
}
