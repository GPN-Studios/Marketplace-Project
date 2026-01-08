<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // rules for each field
        return [
            'name'        => 'required|string|min:5|max:255',
            'description' => 'required|string|max:1000',
            'price'       => 'required|numeric|min:1',
            'stock'       => 'required|integer|min:1',
            'image'       => 'required|image|mimes:jpeg,png,webp|max:2048',
            'tags'        => 'nullable|array',
            'tags.*'      => 'string',
        ];
    }

    public function messages(): array
    {
        // error messages
        return [
            'name.required' => 'O nome do produto é obrigatório.',
            'name.string'   => 'O nome do produto deve ser um texto válido.',
            'name.min'      => 'O nome do produto deve conter no mínimo :min caracteres.',
            'name.max'      => 'O nome do produto não pode ultrapassar :max caracteres.',

            'description.required' => 'A descrição do produto é obrigatória.',
            'description.string'   => 'A descrição deve ser um texto válido.',
            'description.max'      => 'A descrição não pode ultrapassar :max caracteres.',

            'price.required' => 'O preço do produto é obrigatório.',
            'price.numeric'  => 'O preço deve ser um valor numérico.',
            'price.min'      => 'O preço deve ser no mínimo R$ :min.',

            'stock.required' => 'É necessário informar a quantidade em estoque.',
            'stock.integer'  => 'A quantidade deve ser um número inteiro.',
            'stock.min'      => 'A quantidade mínima em estoque é :min unidade.',

            'image.required' => 'É obrigatório inserir uma imagem do produto.',
            'image.image'    => 'O arquivo enviado deve ser uma imagem válida.',
            'image.mimes'    => 'A imagem deve ser do tipo JPEG, PNG ou WEBP.',
            'image.max'      => 'A imagem não pode ultrapassar 2MB.',
        ];
    }
}