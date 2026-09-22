<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // A autorização real (dono do produto) é feita pela ProductPolicy
        // dentro do ProductController::update().
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|min:5|max:255',
            'image' => image_upload_rules(required: false),
            'description' => 'sometimes|string|max:1000',
            'price' => 'sometimes|numeric|min:1',
            'stock' => 'sometimes|integer|min:1',
        ];
    }
}
