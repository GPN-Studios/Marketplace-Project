<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        // A posse do pedido é verificada pela OrderPolicy no controller.
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'recipient_name' => 'required|string|max:255',
            'cep' => 'required|digits:8',
            'state' => 'required|string|size:2',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'number' => 'nullable|string|max:20',
            'complement' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'recipient_name.required' => 'Informe o nome do destinatário.',
            'cep.required' => 'Informe o CEP.',
            'cep.digits' => 'O CEP deve conter 8 dígitos.',
            'state.required' => 'Informe o estado.',
            'state.size' => 'Use a sigla do estado (ex: SP).',
            'city.required' => 'Informe a cidade.',
            'district.required' => 'Informe o bairro.',
            'street.required' => 'Informe a rua.',
        ];
    }
}
