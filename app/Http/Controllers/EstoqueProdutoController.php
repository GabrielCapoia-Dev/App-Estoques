<?php

namespace App\Http\Controllers;

use App\Models\DescarteProduto;
use App\Models\Estoque;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstoqueProdutoController extends Controller
{
    public function create($estoqueId)
    {
        $estoque = Estoque::findOrFail($estoqueId);
        $produtos = Produto::all();

        return view('estoques.produtos.create', compact('estoque', 'produtos'));
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


    public function edit($estoqueId, $pivotId)
    {
        // Busca o estoque para contexto
        $estoque = Estoque::findOrFail($estoqueId);

        // Busca o registro do pivot
        $pivot = DB::table('estoque_produto')->where('id', $pivotId)->first();

        if (!$pivot) {
            return redirect()->route('estoques.produtos.index', $estoqueId)->withErrors('Produto não encontrado no estoque.');
        }

        // Carrega o produto associado
        $produto = Produto::findOrFail($pivot->produto_id);

        return view('estoques.produtos.edit', compact('estoque', 'pivot', 'produto'));
    }

    public function update(Request $request, $estoqueId, $pivotId)
    {
        // Validação dos dados recebidos
        $request->validate([
            'quantidade_atual' => 'required|integer|min:0',
            'quantidade_minima' => 'required|integer|min:0',
            'quantidade_maxima' => 'required|integer|min:0',
            'validade' => 'nullable|date',
        ]);

        // Verifica se o registro do pivot existe
        $pivot = DB::table('estoque_produto')->where('id', $pivotId)->first();

        if (!$pivot) {
            return redirect()->route('estoques.produtos.index', $estoqueId)
                ->withErrors('Registro não encontrado no estoque.');
        }

        // Atualiza os dados diretamente na tabela intermediária
        DB::table('estoque_produto')
            ->where('id', $pivotId)
            ->update([
                'quantidade_atual' => $request->quantidade_atual,
                'quantidade_minima' => $request->quantidade_minima,
                'quantidade_maxima' => $request->quantidade_maxima,
                'validade' => $request->validade, // Aceita `null` se não for enviado
                'updated_at' => now(),
            ]);

        // Redireciona de volta para a lista de produtos no estoque
        return redirect()->route('estoques.show', ['escola' => Estoque::find($estoqueId)->local->id, 'estoque' => $estoqueId])
            ->with('success', 'Produto atualizado com sucesso no estoque!');
    }

}
