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
        return view('profile', compact('user'));
    }
    
    public function update(Request $request) /* RedirectResponse */
    {
        
        dd($request);
        
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
