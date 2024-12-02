<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    /**
     * Retorna todos os usuarios ativos
     */
    public function index(Request $request)
    {
        // Verificando se o usuário clicou no botão para visualizar os inativos
        if ($request->has('mostrar_inativos') && $request->mostrar_inativos == 'true') {
            $usuarios = Usuario::all(); // Pegue todos os usuários, ativos e inativos
        } else {
            $usuarios = Usuario::where('status_usuario', 'Ativo')->get(); // Apenas ativos
        }

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    /**
     * Cria um novo usuário
     */
    public static function store(Request $request, $sobrescreverPermissao = null)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nome_usuario' => 'required|string|min:5|max:30',
                'email_usuario' => [
                    'required',
                    'email',
                    'unique:usuarios',
                    function ($attribute, $value, $fail) {
                        if (strpos($value, ' ') !== false) {
                            $fail('O campo :attribute não pode conter espaços em branco.');
                        }
                        if (strpos($value, '@') === false) {
                            $fail('O campo :attribute deve conter o símbolo @.');
                        }
                        if (strpos($value, '.') === false) {
                            $fail('O campo :attribute deve conter um ponto (.)');
                        }
                    }
                ],
                'senha' => [
                    'required',
                    'string',
                    'min:7',
                    function ($attribute, $value, $fail) {
                        if (!preg_match('/[A-Z]/', $value)) {
                            $fail("A senha deve conter pelo menos uma letra maiúscula.");
                        }
                        if (!preg_match('/[a-z]/', $value)) {
                            $fail("A senha deve conter pelo menos uma letra minúscula.");
                        }
                        if (!preg_match('/\d/', $value)) {
                            $fail("A senha deve conter pelo menos um número.");
                        }
                        if (!preg_match('/[@$!%*?&]/', $value)) {
                            $fail("A senha deve conter pelo menos um caractere especial.");
                        }
                    }
                ],
                'confirmaSenha' => 'required|same:senha',
                'permissao' => 'required|in:Administrador,subAdmin,Gestão,Secretaria,Cozinha,Serviços Gerais',
            ]
        );

        if ($validator->fails()) {
            // Retorna para a view com os erros de validação
            return view('usuario.create', [
                'error' => true,
                'message' => 'Erro ao cadastrar usuário.',
                'errors' => $validator->errors(),
            ]);
        }

        // Criação do novo usuário
        $usuario = Usuario::create([
            'nome_usuario' => $request->nome_usuario,
            'email_usuario' => $request->email_usuario,
            'senha' => bcrypt($request->senha),
            'permissao' => $sobrescreverPermissao ?: $request->permissao,
            'status_usuario' => 'Ativo'
        ]);

        // Redireciona para a página de listagem de usuários, com a mensagem de sucesso
        return redirect()->route('usuarios.index')->with('success', 'Usuário cadastrado com sucesso.');
    }


    /**
     * Retorna o usuario selecionado
     */
    public function show($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return view('usuarios.show', ['message' => 'Usuário não encontrado.']);
        }

        return view('usuarios.show', ['usuario' => $usuario]);
    }


    public function edit($id)
    {
        // Busca o usuário pelo ID
        $usuario = Usuario::findOrFail($id);

        // Retorna a view com os dados do usuário
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id, $sobrescreverPermissao = null)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return redirect()->route('usuarios.index')->with('error', 'Usuário não encontrado.');
        }

        $validator = Validator::make(
            $request->all(),
            [
                'nome_usuario' => 'required|string|min:5|max:30',
                'email_usuario' => [
                    'required',
                    'email',
                    'unique:usuarios,email_usuario,' . $id,
                    function ($attribute, $value, $fail) {
                        if (strpos($value, ' ') !== false) {
                            $fail('O campo :attribute não pode conter espaços em branco.');
                        }
                        if (strpos($value, '@') === false) {
                            $fail('O campo :attribute deve conter o símbolo @.');
                        }
                        if (strpos($value, '.') === false) {
                            $fail('O campo :attribute deve conter um ponto (.)');
                        }
                    }
                ],
                'senha' => [
                    'nullable', // Não obrigatório ao atualizar
                    'string',
                    'min:7',
                    function ($attribute, $value, $fail) {
                        if ($value && !preg_match('/[A-Z]/', $value)) {
                            $fail("A senha deve conter pelo menos uma letra maiúscula.");
                        }
                        if ($value && !preg_match('/[a-z]/', $value)) {
                            $fail("A senha deve conter pelo menos uma letra minúscula.");
                        }
                        if ($value && !preg_match('/\d/', $value)) {
                            $fail("A senha deve conter pelo menos um número.");
                        }
                        if ($value && !preg_match('/[@$!%*?&]/', $value)) {
                            $fail("A senha deve conter pelo menos um caractere especial.");
                        }
                    }
                ],
                'confirmaSenha' => 'nullable|same:senha',
                'permissao' => 'nullable|in:Administrador,subAdmin,Gestão,Secretaria,Cozinha,Serviços Gerais',
                'status_usuario' => 'required|in:Ativo,Inativo'
            ],
            [
                'required' => 'O campo :attribute é obrigatório.',
                'min' => 'O campo :attribute deve conter pelo menos :min caracteres.',
                'max' => 'O campo :attribute deve conter no máximo :max caracteres.',
                'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
                'unique' => 'O campo :attribute deve ser único.',
                'same' => 'O campo :attribute deve ser igual ao campo :other.',
                'in' => 'O campo :attribute deve ser um dos seguintes valores: :values.',
            ],
            [
                'nome_usuario' => 'Nome de Usuário',
                'email_usuario' => 'E-mail de Usuário',
                'senha' => 'Senha',
                'confirmaSenha' => 'Confirmar Senha',
                'permissao' => 'Permissão',
                'status_usuario' => 'Status de Usuário'
            ]
        );

        if ($validator->fails()) {
            // Se houver erro de validação, retorna para a página de edição com erros
            return redirect()->route('usuarios.edit', $usuario->id)
                ->withErrors($validator)
                ->withInput();
        }

        // Atualiza os dados do usuário
        $usuario->update([
            'nome_usuario' => $request->nome_usuario,
            'email_usuario' => $request->email_usuario,
            'senha' => $request->senha ? bcrypt($request->senha) : $usuario->senha,
            'permissao' => $sobrescreverPermissao ?: $request->permissao,
            'status_usuario' => $request->status_usuario
        ]);

        // Retorna para a página de detalhes do usuário com a mensagem de sucesso
        return redirect()->route('usuarios.index')->with('success', 'Usuário editado com sucesso!');
    }


    /**
     * Desabilita o usuário
     */
    public function desativarUsuario($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->status_usuario = 'Inativo';
        $usuario->save();

        return response()->json(['success' => true]);
    }

    /**
     * Habilita o usuário
     */
    public function ativarUsuario($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->status_usuario = 'Ativo';
        $usuario->save();

        return response()->json(['success' => true]);
    }

    /**
     * Atualiza o status do usuário (Ativo/Inativo).
     */
    public function atualizarStatus(Request $request, $id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'error' => true,
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        // Atualiza o status
        $usuario->status_usuario = $request->status_usuario;
        $usuario->save();

        return response()->json([
            'error' => false,
            'message' => 'Status atualizado com sucesso.',
            'usuario' => $usuario
        ], 200);
    }


    /** 
     * Lista todos os usuarios ativos por permissao
     */
    public function visualizarUsuariosPorPermissao($permissao)
    {

        $usuarios = Usuario::where('status_usuario', 'Ativo')->where('permissao', $permissao)->get();

        if ($usuarios->isEmpty()) {
            return view('usuarios.permissao', ['message' => 'Nenhum usuário encontrado.']);
        }

        return view('usuarios.permissao', ['usuarios' => $usuarios, 'permissao' => $permissao]);
    }


    /** 
     * Visualizar todos os usuarios ativos
     */
    public function visualizarUsuariosAtivos()
    {
        $usuarios = Usuario::where('status_usuario', 'Ativo')->where('permissao', '!=', 'Administrador')->get();

        if ($usuarios->isEmpty()) {
            return view('usuarios.ativos', ['message' => 'Nenhum usuário encontrado.']);
        }

        return view('usuarios.ativos', ['usuarios' => $usuarios]);
    }


    /**
     * Visualizar todos os usuarios inativos
     */
    public function visualizarUsuariosInativos()
    {
        $usuarios = Usuario::where('status_usuario', 'Inativo')->where('permissao', '!=', 'Administrador')->get();

        if ($usuarios->isEmpty()) {
            return view('usuarios.inativos', ['message' => 'Nenhum usuário encontrado.']);
        }

        return view('usuarios.inativos', ['usuarios' => $usuarios]);
    }
}
