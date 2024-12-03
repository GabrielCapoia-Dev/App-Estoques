<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Produto;
use Illuminate\Http\Request;

class EstoqueProdutoController extends Controller
{
    public function create($estoqueId)
    {
        $estoque = Estoque::findOrFail($estoqueId);
        $produtos = Produto::all();

        return view('estoque-produto.create', compact('estoque', 'produtos'));
    }

    public function store(Request $request, $estoqueId)
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade_atual' => 'required|integer|min:0',
            'quantidade_minima' => 'required|integer|min:0',
            'quantidade_maxima' => 'required|integer|min:0',
            'validade' => 'nullable|date',
        ]);

        $estoque = Estoque::findOrFail($estoqueId);

        $estoque->produtos()->attach($request->produto_id, [
            'quantidade_atual' => $request->quantidade_atual,
            'quantidade_minima' => $request->quantidade_minima,
            'quantidade_maxima' => $request->quantidade_maxima,
            'validade' => $request->validade,
        ]);

        return redirect()->route('estoques.show', ['escola' => $estoque->local->id, 'estoque' => $estoque->id])
            ->with('success', 'Produto adicionado ao estoque com sucesso!');
    }


    public function edit($estoqueId, $produtoId)
    {
        $estoque = Estoque::findOrFail($estoqueId);
        $produto = $estoque->produtos()->where('produto_id', $produtoId)->firstOrFail();

        return view('estoque-produto.edit', compact('estoque', 'produto'));
    }

    public function update(Request $request, $estoqueId, $produtoId)
    {
        $request->validate([
            'quantidade_atual' => 'required|integer|min:0',
            'quantidade_minima' => 'required|integer|min:0',
            'quantidade_maxima' => 'required|integer|min:0',
            'validade' => 'required|date',
        ]);

        $estoque = Estoque::findOrFail($estoqueId);

        $estoque->produtos()->updateExistingPivot($produtoId, [
            'quantidade_atual' => $request->quantidade_atual,
            'quantidade_minima' => $request->quantidade_minima,
            'quantidade_maxima' => $request->quantidade_maxima,
            'validade' => $request->validade,
        ]);

        return redirect()->route('estoques.show', ['escola' => $estoque->local->id, 'estoque' => $estoque->id])
            ->with('success', 'Produto atualizado com sucesso no estoque!');
    }

    public function baixa($estoque_id, $produto_id)
    {
        $estoque = Estoque::findOrFail($estoque_id);
        $produto = Produto::findOrFail($produto_id);
        // Lógica para dar baixa (remover ou ajustar a quantidade)
        return redirect()->route('estoques.show', $estoque_id)->with('success', 'Baixa realizada com sucesso!');
    }
}
