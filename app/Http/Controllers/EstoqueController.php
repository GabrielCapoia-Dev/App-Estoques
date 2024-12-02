<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Local;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EstoqueController extends Controller
{

    /**
     * O metodo deve ser static pois quando o local é gerado o estoque também deve ser criado junto
     */
    public static function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nome_estoque' => 'required|string|min:2|max:30',
                'descricao_estoque' => 'required|string|min:2|max:255',
            ],
            [
                'required' => 'O campo :attribute é obrigatório.',
                'exists' => 'O campo :attribute nao existe.',
                'string' => 'O campo :attribute deve ser uma string.',
                'in' => 'O campo :attribute deve ser "Ativo" ou "Inativo".',
                'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
                'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            ],
            [
                'nome_estoque' => 'Nome Estoque',
                'descricao_estoque' => 'Descricao Estoque',
            ]
        );


        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }
        // Criando o estoque
        return Estoque::create([
            'nome_estoque' => $request->nome_estoque,
            'status_estoque' => 'Ativo', // O estoque é criado com status Ativo
            'descricao_estoque' => $request->descricao_estoque,
        ]);
    }

    /**
     * Lista os estoques de um local específico
     */
    public function index($local_id)
    {
        $local = Local::findOrFail($local_id);
        $estoques = $local->estoques; // Assume que o relacionamento foi definido no modelo Local

        return view('estoques.index', compact('local', 'estoques'));
    }

    /**
     * Mostra o formulário para criar um novo estoque vinculado a um local
     */
    public function create($local_id)
    {
        $local = Local::findOrFail($local_id);

        return view('estoques.create', compact('local'));
    }

    /**
     * Armazena um novo estoque vinculado a um local
     */
    public function criarEstoque(Request $request, $escola_id)
    {
        $validator = Validator::make($request->all(), [
            'nome_estoque' => 'required|string|min:2|max:30',
            'descricao_estoque' => 'required|string|min:2|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Estoque::create([
            'nome_estoque' => $request->nome_estoque,
            'descricao_estoque' => $request->descricao_estoque,
            'status_estoque' => 'Ativo',
            'escola_id' => $escola_id, // Vincula ao local
        ]);

        return redirect()->route('escolas.estoques', $escola_id)->with('success', 'Estoque criado com sucesso.');
    }


    /**
     * Exibe detalhes de um estoque específico vinculado a um local
     */
    public function show($local_id, $estoque_id)
    {
        $local = Local::findOrFail($local_id);
        $estoque = $local->estoques()->findOrFail($estoque_id);

        return view('estoques.show', compact('local', 'estoque'));
    }

    /**
     * Mostra o formulário para editar um estoque específico
     */
    public function edit($local_id, $estoque_id)
    {
        $local = Local::findOrFail($local_id);
        $estoque = $local->estoques()->findOrFail($estoque_id);

        return view('estoques.edit', compact('local', 'estoque'));
    }

    /**
     * Atualiza um estoque vinculado a um local
     */
    public function update(Request $request, $local_id, $estoque_id)
    {
        $local = Local::findOrFail($local_id);
        $estoque = $local->estoques()->findOrFail($estoque_id);

        $validator = Validator::make(
            $request->all(),
            [
                'nome_estoque' => 'required|string|min:2|max:30',
                'descricao_estoque' => 'required|string|min:2|max:255',
                'status_estoque' => 'required|string|in:Ativo,Inativo',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $estoque->update($request->only('nome_estoque', 'descricao_estoque', 'status_estoque'));

        return redirect()->route('estoques.index', $local_id)->with('success', 'Estoque atualizado com sucesso.');
    }

    /**
     * Desativa um estoque específico de um local
     */
    public function desativarEstoque($local_id, $estoque_id)
    {
        $local = Local::findOrFail($local_id);
        $estoque = $local->estoques()->findOrFail($estoque_id);

        $estoque->update(['status_estoque' => 'Inativo']);
        ProdutoController::estoqueInativadoInativarProdutos($estoque_id);

        return redirect()->route('estoques.index', $local_id)->with('success', 'Estoque desativado com sucesso.');
    }

    /**
     * Ativa um estoque específico de um local
     */
    public function ativarEstoque($local_id, $estoque_id)
    {
        $local = Local::findOrFail($local_id);
        $estoque = $local->estoques()->findOrFail($estoque_id);

        $estoque->update(['status_estoque' => 'Ativo']);
        ProdutoController::estoqueAtivadoAtivarProdutos($estoque_id);

        return redirect()->route('estoques.index', $local_id)->with('success', 'Estoque ativado com sucesso.');
    }

    public function estoquesPorEscola($escola_id)
    {
        $escola = Local::findOrFail($escola_id); // Supondo que "Local" seja o modelo de escolas
        $estoques = $escola->estoques; // Relacionamento "hasMany" no modelo Local

        return view('estoques.index', compact('escola', 'estoques'));
    }
}
