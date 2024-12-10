<?php

namespace App\Http\Controllers;

use App\Models\DescarteProduto;
use App\Models\Estoque;
use App\Models\EstoqueProduto;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstoqueProdutoController extends Controller
{
    public function create($estoqueId)
    {
        $estoque = Estoque::find($estoqueId);
        $produtos = Produto::all();

        // Criando uma estrutura de dados com produto_id como chave
        $produtosEstoque = $estoque->produtos->reduce(function ($carry, $produto) {
            $carry[$produto->id] = [
                'quantidade_minima' => $produto->pivot->quantidade_minima,
                'quantidade_maxima' => $produto->pivot->quantidade_maxima
            ];
            return $carry;
        }, []);

        return view('estoques.produtos.create', compact('estoque', 'produtos', 'produtosEstoque'));
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
        // Validar os dados de entrada
        $validated = $request->validate([
            'quantidade_atual' => 'required|integer|min:0',
            'quantidade_minima' => 'required|integer|min:0',
            'quantidade_maxima' => 'required|integer|min:0',
            'validade' => 'nullable|date',
        ]);

        // Encontrar o pivot (registro específico no estoque)
        $pivot = EstoqueProduto::findOrFail($pivotId);

        // Atualizar o pivot com os dados recebidos
        $pivot->quantidade_atual = $validated['quantidade_atual'];
        $pivot->quantidade_minima = $validated['quantidade_minima'];
        $pivot->quantidade_maxima = $validated['quantidade_maxima'];
        $pivot->validade = $validated['validade'];
        $pivot->save();

        // Atualizar os outros registros do mesmo produto no estoque, mas apenas se a quantidade atual for maior que 0
        $produtosEstoque = EstoqueProduto::where('produto_id', $pivot->produto_id)
            ->where('estoque_id', $estoqueId)
            ->where('id', '!=', $pivotId) // Exclui o próprio registro
            ->where('quantidade_atual', '>', 0) // Adicionando a condição de quantidade atual > 0
            ->get();

        foreach ($produtosEstoque as $produtoEstoque) {
            $produtoEstoque->quantidade_minima = $validated['quantidade_minima'];
            $produtoEstoque->quantidade_maxima = $validated['quantidade_maxima'];
            $produtoEstoque->save();
        }

        // Redirecionar após a atualização
        return redirect()->route('estoques.show', ['escola' => $pivot->estoque->local->id, 'estoque' => $estoqueId])
            ->with('success', 'Produto atualizado com sucesso!');
    }
}
