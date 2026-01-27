<?php

namespace App\Http\Controllers;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;

class UserController extends Controller
{
    public function profile(User $user): View
    {
        $user->load('orders.items.product');

        return view('profile', compact('user'));
    }

    public function userProducts()
    {
        $user = Auth::user();
        $user->load('products');

        return view('user_products');
    }

    public function update(Request $request, User $user) /* RedirectResponse */
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'name'  => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ],
        [
            'name.string' => 'O nome deve ser uma sequência de caracteres.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
        ]);

        if($data['password']) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update(array_filter($data));

        return back()->with('success', 'Dados alterados com sucesso.');
    }

    public function pfpupdate(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,webp|max:2048',
        ],
        [
            'profile_picture.required' => 'Insira uma imagem válida.',
            'profile_picture.image'    => 'Insira uma imagem válida.',
            'profile_picture.mimes'    => 'A imagem deve ser do tipo JPEG, PNG ou WEBP.',
        ]
        );

        $user->update(['profile_picture' => $data['profile_picture']->store('profile_pictures', 'public')]);


        return back()->with('success', 'Foto de perfil alterada com sucesso.');
    }
}
