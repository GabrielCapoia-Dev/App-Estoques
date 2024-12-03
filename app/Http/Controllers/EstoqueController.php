<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Local;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EstoqueController extends Controller
{

    /**
     * Lista os estoques de um local específico
     */
    public function index($id_local)
    {
        $escola = Local::find($id_local);

        if (!$escola) {
            return redirect()->route('escolas.index')->with('error', 'Local não encontrado');
        }

        // Agora buscamos os estoques relacionados ao local pelo campo id_local
        $estoques = Estoque::where('id_local', $id_local)->get();

        return view('estoques.index', compact('estoques', 'escola'));
    }

    /**
     * Mostra o formulário para criar um novo estoque vinculado a um local
     */
    public function create($local_id)
    {
        $escola = Local::findOrFail($local_id);

        return view('estoques.create', compact('escola'));
    }

    /**
     * Armazena um novo estoque vinculado a um local
     */
    public function criarEstoque(Request $request, $local_id)
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
            'id_local' => $local_id,
        ]);

        return redirect()->route('estoques.index', $local_id)->with('success', 'Estoque criado com sucesso.');
    }

    /**
     * Exibe detalhes de um estoque específico vinculado a um local
     */
    public function show($escolaId, $estoqueId)
    {
        $estoque = Estoque::findOrFail($estoqueId);
        $escola = $estoque->local;  // Como o estoque está relacionado com local (escola)

        return view('estoques.show', compact('estoque', 'escola'));
    }


    /**
     * Mostra o formulário para editar um estoque específico
     */
    public function edit($local_id, $estoque_id)
    {
        // Buscar o estoque específico pelo ID
        $estoque = Estoque::find($estoque_id);

        // Verificar se o estoque foi encontrado
        if (!$estoque) {
            return redirect()->route('estoques.index', $local_id)
                ->with('error', 'Estoque não encontrado');
        }

        // Passar o estoque encontrado para a view
        return view('estoques.edit', compact('estoque'));
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
}
