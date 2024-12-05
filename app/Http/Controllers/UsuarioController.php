<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    /**
     * Retorna todos os usuarios ativos
     */
    public function index(Request $request)
    {
        if ($request->has('mostrar_inativos') && $request->mostrar_inativos == 'true') {
            $usuarios = Usuario::where('status_usuario', 'Inativo')->get();
        } else {
            $usuarios = Usuario::where('status_usuario', 'Ativo')->get();
        }

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    /**
     * Cria um novo usuário
     */
    public function store(Request $request)
    {
        // Validação dos dados de entrada
        $validatedData = $request->validate([
            'nome_usuario' => 'required|string|min:5|max:30',
            'email_usuario' => 'required|email|unique:usuarios|max:255',
            'senha' => [
                'required',
                'string',
                'min:7',
                'max:16',
                'regex:/[A-Z]/', // Letra maiúscula
                'regex:/[a-z]/', // Letra minúscula
                'regex:/\d/',    // Número
                'regex:/[@$!%*?&]/' // Caractere especial
            ],
            'confirmaSenha' => 'required|same:senha',
            'permissao' => 'required|in:Administrador,subAdmin,Gestão,Secretaria,Cozinha,Serviços Gerais',
        ]);

        Usuario::create([
            'nome_usuario' => $validatedData['nome_usuario'],
            'email_usuario' => $validatedData['email_usuario'],
            'senha' => bcrypt($validatedData['senha']),
            'permissao' => $validatedData['permissao'],
            'status_usuario' => 'Ativo',
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuário cadastrado com sucesso.');
    }



    /**
     * Retorna o usuario selecionado
     */
    public function show($id)
    {
        $usuario = Usuario::find($id);
        $locais = $usuario->locais()->get();

        if (!$usuario || !$locais) {
            return view('usuarios.show', ['message' => 'Usuário não encontrado.']);
        }

        return view('usuarios.show', ['usuario' => $usuario, 'locais' => $locais]);
    }


    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);

        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        // Validação dos dados
        $validatedData = $request->validate([
            'nome_usuario' => 'required|string|min:5|max:30',
            'email_usuario' => [
                'required',
                'email',
                Rule::unique('usuarios')->ignore($usuario->id), // Ignora o email do próprio usuário
            ],
            'senha' => [
                'nullable', // Campo opcional
                'string',
                'min:7',
                'max:16',
                'regex:/[A-Z]/', // Letra maiúscula
                'regex:/[a-z]/', // Letra minúscula
                'regex:/\d/',    // Número
                'regex:/[@$!%*?&]/' // Caractere especial
            ],
            'confirmaSenha' => 'nullable|same:senha', // Opcional e deve coincidir com a senha
            'permissao' => 'required|in:Administrador,subAdmin,Gestão,Secretaria,Cozinha,Serviços Gerais',
            'status_usuario' => 'required|in:Ativo,Inativo',
        ]);

        // Atualização dos dados
        $usuario->update([
            'nome_usuario' => $validatedData['nome_usuario'],
            'email_usuario' => $validatedData['email_usuario'],
            'permissao' => $validatedData['permissao'],
            'status_usuario' => $validatedData['status_usuario'],
        ]);

        // Atualiza a senha se estiver preenchida
        if (!empty($validatedData['senha'])) {
            $usuario->senha = bcrypt($validatedData['senha']);
            $usuario->save();
        }

        // Redireciona com mensagem de sucesso
        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso.');
    }
}
