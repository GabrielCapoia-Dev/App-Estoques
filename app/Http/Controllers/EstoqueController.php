<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Local;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EstoqueController extends Controller
{

    /**
     * Lista os estoques de um local específico com base no status.
     */
    public function index(Request $request, $id_local)
    {
        // Verifica se o local existe
        $escola = Local::find($id_local);


        if (!$escola) {
            return redirect()->route('escolas.index')->with('error', 'Local não encontrado');
        }

        $status = $request->get('status_estoque', 'Ativo');

        if ($status === 'Inativo') {
            $estoques = Estoque::where('id_local', $id_local)->where('status_estoque', 'Inativo')->get();
        } else {
            $estoques = Estoque::where('id_local', $id_local)->where('status_estoque', 'Ativo')->get();
        }




        $valorTotalBaixa = 0;
        $valorTotalEstoque = 0;

        $baixaController = new DescarteProdutoController();

        // Calcula os totais para estoques filtrados
        foreach ($estoques as $estoque) {
            $idEstoque = $estoque->id;

            // Obtém o total de estoque
            $totalEstoque = $this->show($escola->id, $idEstoque)['totalEstoque'];

            // Obtém o total de baixas
            $baixa = $baixaController->show($idEstoque);

            $valorTotalBaixa += floatval(str_replace(',', '.', str_replace('.', '', $baixa['totalBaixas'] ?? '0')));
            $valorTotalEstoque += floatval(str_replace(',', '.', str_replace('.', '', $totalEstoque ?? '0')));
        }

        // Formata os valores finais para exibição
        $totalBaixa = number_format($valorTotalBaixa, 2, ',', '.');
        $totalEstoque = number_format($valorTotalEstoque, 2, ',', '.');

        // Retorna a view com os dados filtrados
        return view('estoques.index', compact('estoques', 'escola', 'totalBaixa', 'totalEstoque'));
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
        $estoque = Estoque::with(['produtos' => function ($query) {
            $query->wherePivot('quantidade_atual', '>', 0)
                ->withPivot('id', 'quantidade_atual', 'quantidade_minima', 'quantidade_maxima', 'validade');
        }])->findOrFail($estoqueId);

        $totalEstoque = $estoque->produtos->reduce(function ($carry, $produto) {
            $quantidadeAtual = floatval(str_replace(',', '.', $produto->pivot->quantidade_atual));
            $preco = floatval(str_replace(',', '.', $produto->preco));


            if ($quantidadeAtual > 0 && $preco > 0) {
                $valorTotalProduto = $quantidadeAtual * $preco;

                $carry += $valorTotalProduto;
            }

            return $carry;
        }, 0);


        $totalEstoque = number_format($totalEstoque, 2, ',', '.');

        $baixa = new DescarteProdutoController();

        $totalBaixa = $baixa->show($estoqueId)['totalBaixas'];

        $escola = $estoque->local;

        return view('estoques.show', compact('estoque', 'escola', 'totalEstoque', 'totalBaixa'));
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
