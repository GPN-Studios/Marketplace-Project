<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:80'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ],
        [
            //name
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser um texto.',
            'name.min' => 'O campo nome deve ter no mínimo 3 caracteres.',
            'name.max' => 'O campo nome excede o limite de caracteres.',

            //email
            'email.required' => 'O campo email é obrigatório',
            'email.string'=> 'O campo email deve ser um texto válido.', 
            'email.unique' => 'O campo email já está em uso.',
            'email.max' => 'O campo email excede o limite de caracteres.',

            //password
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'As senhas devem ser iguais.',
            'password.min' => 'A senha deve ter no mínimo :min caracteres,'
        ]
        )->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
