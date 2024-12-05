<?php

namespace App\Http\Controllers;

use App\Models\HistoricoProduto;
use Illuminate\Http\Request;

class HistoricoProdutoController extends Controller
{
    public function index($estoqueId, $produtoId)
    {
        // Supondo que o modelo de Histórico esteja relacionado com Produto e Estoque
        $historico = HistoricoProduto::where('estoque_id', $estoqueId)
            ->where('produto_id', $produtoId)
            ->get();

        return response()->json($historico);
    }

    /**
     * A função store do historico deve ser static pois ela é chamada dentro da update de produto
     * sempre que o produto é atualizado o historico também é atualizado
     */
    public static function store(Request $request)
    {
        $historico = new HistoricoProduto();
        $historico->id_produto = $request->id_produto;
        $historico->nome_produto = $request->nome_produto;
        $historico->status_produto = $request->status_produto;
        $historico->descricao_produto = $request->descricao_produto;
        $historico->preco = $request->preco;
        $historico->quantidade_atual = $request->quantidade_atual;
        $historico->quantidade_minima = $request->quantidade_minima;
        $historico->quantidade_maxima = $request->quantidade_maxima;
        $historico->validade = $request->validade;
        $historico->save();

        return $historico;
    }

    /**
     * Visualizar um unico item especifico do historico
     */
    public function show($id)
    {
        $historico = HistoricoProduto::find($id);

        if (!$historico) {
            return response()->json([
                'error' => true,
                'message' => 'Nenhum item encontrado.'
            ], 404);
        }

        return response()->json([
            'error' => false,
            'message' => 'Item encontrado.',
            'historico' => $historico
        ], 200);
    }

    /**
     * Visualizar todo o historico de um produto
     */
    public function visuzalizarHistoricoDeProduto($id)
    {
        $historicoProduto = HistoricoProduto::where('id_produto', $id)->get();

        if (!$historicoProduto) {
            return response()->json([
                'error' => true,
                'message' => 'Nenhum item encontrado.'
            ], 404);
        }

        return response()->json([
            'error' => false,
            'message' => 'Itens encontrados.',
            'historico' => $historicoProduto
        ], 200);
    }
}
