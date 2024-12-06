<?php

namespace App\Http\Controllers;

use App\Models\EstoqueProduto;
use App\Models\DescarteProdutos;
use App\Models\Estoque;
use App\Models\Produto;
use Illuminate\Http\Request;

class DescarteProdutoController extends Controller
{

    /**
     * Exibir todas as baixas de um estoque
     */

    public function index($estoqueId)
    {
        $estoque = Estoque::with(['produtos' => function ($query) {
            $query->wherePivot('quantidade_atual', '>', 0)
                ->withPivot('id', 'quantidade_atual', 'quantidade_minima', 'quantidade_maxima', 'validade');
        }])->findOrFail($estoqueId);

        $estoque_produto = EstoqueProduto::where("estoque_id", $estoqueId)->get();

        $estoqueProdutoIds = $estoque_produto->pluck('id');

        $produtosIds = $estoque_produto->pluck('produto_id');

        $produtos = Produto::whereIn('id', $produtosIds)->get();

        $baixas = DescarteProdutos::with('produto')
            ->whereIn('id_estoque_produto', $estoqueProdutoIds)
            ->orderBy('created_at', 'desc')
            ->get();

        $dadosCompletos = $baixas->map(function ($baixa) use ($produtos, $estoque_produto) {
            $estoqueProduto = $estoque_produto->firstWhere('id', $baixa->id_estoque_produto);

            $produto = $produtos->firstWhere('id', $estoqueProduto->produto_id);

            return [
                'id_estoque_produto' => $estoqueProduto->id,
                'nome_produto' => $produto ? $produto->nome_produto : 'Produto não encontrado',
                'quantidade_descarte' => $baixa->quantidade_descarte,
                'defeito_descarte' => $baixa->defeito_descarte ?? 'Não especificado',
                'descricao_descarte' => $baixa->descricao_descarte ?? 'Sem descrição',
                'validade' => $estoqueProduto->validade
                    ? \Carbon\Carbon::createFromFormat('Y-m-d', $estoqueProduto->validade)->format('d/m/Y')
                    : 'Sem validade',
                'created_at' => $baixa->created_at->format('d/m/Y H:i')
            ];
        });

        return view('baixas.index', compact('dadosCompletos', 'estoque'));
    }




    /**
     * Processa o descarte do produto no estoque.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $estoqueId
     * @param  int  $pivotId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $estoqueId, $pivotId)
    {
        $request->validate([
            'quantidade_descarte' => 'required|integer|min:1',
        ]);

        $estoqueProduto = EstoqueProduto::where('id', $pivotId)
            ->where('estoque_id', $estoqueId)
            ->first();

        if (!$estoqueProduto) {
            return redirect()->back()->withErrors('Produto não encontrado no estoque.');
        }

        if ($request->quantidade_descarte > $estoqueProduto->quantidade_atual) {
            return redirect()->back()->withErrors('A quantidade a ser descartada é maior do que a quantidade disponível.');
        }

        $descarte = DescarteProdutos::create([
            'id_estoque_produto' => $pivotId,
            'quantidade_descarte' => $request->quantidade_descarte,
            'defeito_descarte' => $request->defeito_descarte ?? 'Não informado',
            'descricao_descarte' => $request->descricao_descarte ?? 'Não informado',
        ]);


        $estoqueProduto->quantidade_atual -= $request->quantidade_descarte;
        $estoqueProduto->save();

        return response()->json([
            'success' => 'Produto descartado com sucesso.',
            'descarte' => $descarte
        ]);
    }
}
