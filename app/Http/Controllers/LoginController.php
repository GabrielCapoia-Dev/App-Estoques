<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessTokenResult;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->only('email_usuario', 'senha');

        if (Auth::attempt(['email_usuario' => $credentials['email_usuario'], 'password' => $credentials['senha']])) {
            $usuario = Auth::user();

            dd(Auth::check());
            session()->put('user', [
                'id' => $usuario->id,
                'name' => $usuario->name_usuario,
                'email' => $usuario->email_usuario,
                'permissao' => $usuario->permissao
            ]);

            return redirect()->route('usuarios.index')->with('message', 'Login realizado com sucesso');
        }

        return redirect()->route('login')->withErrors(['message' => 'Credenciais inválidas']);
    }



    public function logout(Request $request)
    {
        // Desloga o usuário
        Auth::logout();
        $request->session()->flush(); // Limpa todos os dados da sessão

        return redirect()->route('login')->with('message', 'Logout realizado com sucesso');
    }
}
