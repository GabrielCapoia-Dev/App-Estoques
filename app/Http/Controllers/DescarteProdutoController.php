<?php

namespace App\Http\Controllers;

use App\Models\EstoqueProduto;
use App\Models\DescarteProduto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DescarteProdutoController extends Controller
{
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
        // Valida se a quantidade de descarte é válida
        $request->validate([
            'quantidade_descarte' => 'required|integer|min:1',
        ]);

        // Recupera o produto no estoque
        $estoqueProduto = EstoqueProduto::where('id', $pivotId)
            ->where('estoque_id', $estoqueId)
            ->first();

        if (!$estoqueProduto) {
            return redirect()->back()->withErrors('Produto não encontrado no estoque.');
        }

        // Verifica se a quantidade de descarte não é maior que a quantidade disponível
        if ($request->quantidade_descarte > $estoqueProduto->quantidade_atual) {
            return redirect()->back()->withErrors('A quantidade a ser descartada é maior do que a quantidade disponível.');
        }

        // Inicia uma transação para garantir que tudo aconteça de forma atômica
        DB::beginTransaction();

        try {
            // Cria o registro de descarte
            DescarteProduto::create([
                'id_estoque_produto' => $pivotId,
                'quantidade_descarte' => $request->quantidade_descarte,
                'defeito_descarte' => $request->defeito_descarte ?? 'Não informado',
                'descricao_descarte' => $request->descricao_descarte ?? 'Não informado',
            ]);


            // Atualiza a quantidade do produto no estoque
            $estoqueProduto->quantidade_atual -= $request->quantidade_descarte;
            $estoqueProduto->save();

            // Commit da transação
            DB::commit();

            // Log para verificação no console

            // Retorna para a página de detalhes do produto com a mensagem de sucesso
            return redirect()->route('estoques.produtos.show', ['estoque' => $estoqueId, 'pivotId' => $pivotId])
                ->with('success', 'Produto descartado com sucesso.');
        } catch (\Exception $e) {
            // Em caso de erro, faz o rollback da transação
            DB::rollback();
            // Log do erro

            return redirect()->back()->withErrors('Erro ao processar o descarte. Tente novamente.');
        }
    }
}
