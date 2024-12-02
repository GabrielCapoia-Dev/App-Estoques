<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Local;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PgSql\Lob;

class LocalController extends Controller
{
    /**
     * Visualizar todos os locais
     */
    public function index()
    {
        $escola = Local::all();

        if (!$escola) {
            return response()->json([
                'error' => true,
                'message' => 'Nenhum local encontrado.'
            ], 404);
        }

        return view('escolas.index', ['escolas' => $escola]);
    }

    public function create()
    {
        return view('escolas.create');
    }

    /**
     * Cria um novo local chamando os metodos static de endereco e estoque
     */
    public function store(Request $request)
    {
        // Cria o endereço
        $endereco = EnderecoController::store($request);
        if ($endereco instanceof \Illuminate\Http\JsonResponse) {
            return $endereco;  // Se a resposta for um erro JSON, retorne imediatamente
        }

        // Cria o estoque
        $estoque = EstoqueController::store($request);
        if ($estoque instanceof \Illuminate\Http\JsonResponse) {
            return $estoque;  // Se a resposta for um erro JSON, retorne imediatamente
        }


        // Validar os dados da Escola
        $validator = Validator::make(
            $request->all(),
            [
                'nome_local' => 'required|string|min:2|max:30',
                'status_local' => 'required|string|in:Ativo,Inativo',
            ],
            [
                'required' => 'O campo :attribute é obrigatório.',
                'string' => 'O campo :attribute deve ser uma string.',
                'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
                'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
                'in' => 'O campo :attribute deve ser um dos seguintes valores: :values.',
            ],
            [
                'nome_local' => 'Nome do local',
                'status_local' => 'Status do local',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $escola = Local::create([
            'nome_local' => $request->nome_local,
            'status_local' => $request->status_local,
            'id_endereco' => $endereco->id,
            'id_estoque' => $estoque->id,
        ]);

        return view('escolas.index', [
            'escolas' => $escola,
            'endereco' => $endereco,
            'estoque' => $estoque,
        ]);
    }

    /**
     * Atualiza o local
     */
    public function update(Request $request, $id)
    {
        // Busca o local e o endereço relacionado
        $local = Local::with('endereco')->findOrFail($id);
        $endereco = $local->endereco; // Acessa o endereço relacionado ao local

        // Validação dos dados
        $validator = Validator::make(
            $request->all(),
            [
                'nome_local' => 'required|string|min:2|max:30',
                'status_local' => 'required|string|in:Ativo,Inativo',
                'cep' => 'required|string|size:8',
                'numero' => 'required|string|max:10',
                'logradouro' => 'required|string|max:100',
                'bairro' => 'required|string|max:100',
                'cidade' => 'required|string|max:100',
                'estado' => 'required|string|max:100',
                'complemento' => 'nullable|string|max:100',
            ],
            [
                'required' => 'O campo :attribute é obrigatório.',
                'size' => 'O campo :attribute deve ter :size caracteres.',
                'max' => 'O campo :attribute não pode ter mais de :max caracteres.',
                'string' => 'O campo :attribute deve ser uma string.',
                'in' => 'O campo :attribute deve ser um dos seguintes valores: :values.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->route('escolas.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        // Atualiza o endereço
        $endereco->update($request->only([
            'cep',
            'numero',
            'logradouro',
            'bairro',
            'cidade',
            'estado',
            'complemento'
        ]));

        // Atualiza o local
        $local->update($request->only(['nome_local', 'status_local']));

        // Mensagem de sucesso
        return redirect()->route('escolas.index')->with('success', 'Local e endereço atualizados com sucesso.');
    }

    /**
     * Exibe a lista de funcionários vinculados a uma escola.
     */
    public function show($id)
    {
        $local = Local::findOrFail($id);
        $usuarios = Usuario::all();  // Obtém todos os usuários

        return view('escolas.show', compact('local', 'usuarios'));
    }

    /**
     * Vincula um usuário já existente a um local (escola).
     */
    public function vincularUsuario(Request $request, $local_id)
    {
        $local = Local::findOrFail($local_id);

        // Validação: Verifica se o usuário existe
        $usuario = Usuario::find($request->usuario_id);

        if (!$usuario) {
            return redirect()->back()->with('error', 'Usuário não encontrado!');
        }

        // Vincula o usuário ao local
        $local->usuarios()->attach($usuario);

        return redirect()->route('escolas.show', $local->id)->with('success', 'Funcionário vinculado com sucesso!');
    }

    /**
     * Desvincula um usuário de um local (escola).
     */
    public function desvincularUsuario($local_id, $usuario_id)
    {
        $local = Local::findOrFail($local_id);
        $usuario = Usuario::findOrFail($usuario_id);

        // Remove a associação do usuário com o local
        $local->usuarios()->detach($usuario);

        return redirect()->route('escolas.show', $local->id)->with('success', 'Funcionário desvinculado com sucesso!');
    }


    public function listarFuncionarios($id)
    {
        // Encontra o local com os usuários associados
        $local = Local::with('usuarios')->findOrFail($id);

        // Passa o local e seus funcionários para a view
        return view('escolas.show', compact('local'));
    }

    public function edit($id)
    {
        // Busca o local e o endereço relacionado pelo ID
        $local = Local::with('endereco')->findOrFail($id);

        // Retorna a view com os dados do local e endereço
        return view('escolas.edit', compact('local'));
    }

    /**
     * Desativa o local
     */
    public function desativarLocal($id)
    {
        $local = Local::find($id);

        if (!$local) {
            return response()->json([
                'error' => true,
                'message' => 'Local não encontrado.'
            ], 404);
        }

        $local->status_local = 'Inativo';
        $local->save();
        return response()->json([
            'error' => false,
            'message' => 'Local desativado com sucesso.',
            'local' => $local
        ], 200);
    }


    /**
     * Ativa o local
     */
    public function ativarLocal($id)
    {
        $local = Local::find($id);

        if (!$local) {
            return response()->json([
                'error' => true,
                'message' => 'Local não encontrado.'
            ], 404);
        }

        $local->status_local = 'Ativo';
        $local->save();
        return response()->json([
            'error' => false,
            'message' => 'Local ativado com sucesso.',
            'local' => $local
        ], 200);
    }


    /**
     * Retorna os estoques ativos do local
     */
    public function visualizarEstoquesAtivosDoLocal($id)
    {
        $local = Local::find($id);

        if (!$local) {
            return response()->json([
                'error' => true,
                'message' => 'Local não encontrado.'
            ], 404);
        }

        $estoques = $local->estoques()->where('status', 'Ativo')->get();

        return response()->json([
            'error' => false,
            'message' => 'Estoques ativos do local.',
            'estoques' => $estoques
        ], 200);
    }

    /**
     * Visualizar estoques inativos do local
     */
    public function visualizarEstoquesInativosDoLocal($id)
    {
        $local = Local::find($id);

        if (!$local) {
            return response()->json([
                'error' => true,
                'message' => 'Local não encontrado.'
            ], 404);
        }

        $estoques = $local->estoques()->where('status', 'Inativo')->get();

        return response()->json([
            'error' => false,
            'message' => 'Estoques inativos do local.',
            'estoques' => $estoques
        ], 200);
    }
}
