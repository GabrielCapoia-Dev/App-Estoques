<?php

namespace App\Http\Controllers;

use App\Models\EstoqueProduto;
use App\Models\DescarteProdutos;
use App\Models\Estoque;
use App\Models\Local;
use App\Models\Produto;
use Illuminate\Http\Request;

class DescarteProdutoController extends Controller
{

    /**
     * Lista todos os valores de todas as escolas.
     */
    public function index()
    {
        $valorTotalBaixaGeral = 0;
        $valorTotalEstoqueGeral = 0;

        $resultado = [];
        $estoqueController = new EstoqueController(); // Certifique-se de que este controller existe e implementa o método necessário
        $locals = Local::all(); // Obtém todos os locais

        foreach ($locals as $local) {
            $estoques = Estoque::where('id_local', $local->id)->get();

            foreach ($estoques as $estoque) {
                $idEstoque = $estoque->id;

                // Obtém os totais de estoque e baixas para cada estoque
                $totalEstoque = $estoqueController->index($local->id)['totalEstoque'] ?? 0;
                $totalBaixas = $this->show($local->id, $idEstoque)['totalBaixas'] ?? 0;

                // Converte valores para float
                $valorEsto = floatval(str_replace(',', '.', $totalEstoque));
                $valorBai = floatval(str_replace(',', '.', $totalBaixas));

                // Atualiza os totais gerais
                $valorTotalEstoqueGeral += $valorEsto;
                $valorTotalBaixaGeral += $valorBai;
            }
            // Salva os dados no array de resultados
            $resultado[] = [
                'idLocal' => $local->id,
                'local' => $local->nome_local,
                'valorTotalEstoque' => $valorEsto,
                'valorTotalBaixa' => $valorBai,
            ];
        }

        // Formatação dos totais gerais
        $totalBaixaGeralFormatado = number_format($valorTotalBaixaGeral, 2, ',', '.');
        $totalEstoqueGeralFormatado = number_format($valorTotalEstoqueGeral, 2, ',', '.');

        // Retorna a view com os dados
        return view('baixas.index', compact('resultado', 'totalBaixaGeralFormatado', 'totalEstoqueGeralFormatado'));
    }



    /**
     * Exibir todas as baixas de um estoque
     */
    public function show($estoqueId)
    {
        $estoque = Estoque::with(['produtos' => function ($query) {
            $query->wherePivot('quantidade_atual', '>', 0)
                ->withPivot('id', 'quantidade_atual', 'quantidade_minima', 'quantidade_maxima', 'validade');
        }])->findOrFail($estoqueId);


        $estoque_produto = EstoqueProduto::where("estoque_id", $estoqueId)->get();
        $estoqueProdutoIds = $estoque_produto->pluck('id');
        $produtosIds = $estoque_produto->pluck('produto_id');
        $produtos = Produto::whereIn('id', $produtosIds)->get();

        // Consultar as baixas
        $baixas = DescarteProdutos::with('produto')
            ->whereIn('id_estoque_produto', $estoqueProdutoIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // Mapear as baixas com os dados completos
        $dadosCompletos = $baixas->map(function ($baixa) use ($produtos, $estoque_produto) {
            // Encontrar o estoqueProduto relacionado à baixa
            $estoqueProduto = $estoque_produto->firstWhere('id', $baixa->id_estoque_produto);

            // Encontrar o produto relacionado ao estoqueProduto
            $produto = $produtos->firstWhere('id', $estoqueProduto->produto_id);


            // Adicionar dados da baixa no array
            return [
                'baixas' => [
                    'id' => $baixa->id,
                    'id_estoque_produto' => $estoqueProduto->id,
                    'nome_produto' => $produto ? $produto->nome_produto : 'Produto não encontrado',
                    'quantidade_descarte' => $baixa->quantidade_descarte,
                    'defeito_descarte' => $baixa->defeito_descarte ?? 'Não especificado',
                    'descricao_descarte' => $baixa->descricao_descarte ?? 'Sem descrição',
                    'validade' => $estoqueProduto->validade
                        ? \Carbon\Carbon::createFromFormat('Y-m-d', $estoqueProduto->validade)->format('d/m/Y')
                        : 'Sem validade',
                    'created_at' => $baixa->created_at->format('d/m/Y')
                ],

                'produtos' => [
                    'id_produto' => $estoqueProduto->produto_id,
                    'preco_produto' => $produto ? $produto->preco : 0,
                    'quantidade_descarte' => $baixa->quantidade_descarte,
                ]
            ];
        });
        $totalBaixas = $this->calcularValorTotal($dadosCompletos->toArray());
        $escola = $estoque['id'];

        // Retornar os dados para a view
        return view('baixas.show', compact('dadosCompletos', 'estoque', 'totalBaixas', 'escola'));
    }



    /**
     * Filtrar baixas do estoque
     */
    public function filtrarBaixas(Request $request, $estoqueId)
    {
        // Encontrar o estoque
        $estoque = Estoque::find($estoqueId);

        if (!$estoque) {
            return redirect()->route('estoques.index')->with('error', 'Estoque não encontrado.');
        }

        // Validação dos filtros
        $request->validate([
            'defeito_descarte' => 'nullable|string',
            'validade-Inicio' => 'nullable|date',
            'validade-Fim' => 'nullable|date|after_or_equal:validade-Inicio',
        ]);

        // Obter todos os dados do método show
        $allData = $this->show($estoqueId, true); // Usamos `true` para indicar que queremos os dados, não a view.

        // Aplicar os filtros
        $dadosFiltrados = collect($allData['dadosCompletos']);

        if ($request->filled('defeito_descarte')) {
            $dadosFiltrados = $dadosFiltrados->filter(function ($item) use ($request) {
                // Verifica se o defeito_descarte está no array de 'baixas'
                return stripos($item['baixas']['defeito_descarte'], $request->input('defeito_descarte')) !== false;
            });
        }

        if ($request->filled('validade-Inicio')) {
            $dadosFiltrados = $dadosFiltrados->filter(function ($item) use ($request) {
                // Comparar a data de validade dentro do array 'baixas'
                return \Carbon\Carbon::createFromFormat('d/m/Y', $item['baixas']['validade']) >= \Carbon\Carbon::createFromFormat('Y-m-d', $request->input('validade-Inicio'));
            });
        }

        if ($request->filled('validade-Fim')) {
            $dadosFiltrados = $dadosFiltrados->filter(function ($item) use ($request) {
                // Comparar a data de validade dentro do array 'baixas'
                return \Carbon\Carbon::createFromFormat('d/m/Y', $item['baixas']['validade']) <= \Carbon\Carbon::createFromFormat('Y-m-d', $request->input('validade-Fim'));
            });
        }
        $escola = $estoque['id'];
        $totalBaixas = $this->calcularValorTotal($dadosFiltrados->toArray());

        // Retornar a view com os dados filtrados
        return view('baixas.show', [
            'dadosCompletos' => $dadosFiltrados,
            'estoque' => $allData['estoque'],
            'totalBaixas' => $totalBaixas,
            'escola' => $escola
        ]);
    }




    public function calcularValorTotal(array $dadosCompletos)
    {
        $valorTotal = 0;

        // Iterar sobre os dados completos
        foreach ($dadosCompletos as $dado) {
            // Somar a quantidade de descarte do produto (garantindo que seja um número)
            $quantidadeDescarte = floatval($dado['produtos']['quantidade_descarte']);

            // Obter o preço do produto (garantindo que seja um número)
            $precoProduto = floatval(str_replace(',', '.', $dado['produtos']['preco_produto'])); // Garantir que seja interpretado corretamente

            // Calcular o valor total de cada item
            $valorTotal += $quantidadeDescarte * $precoProduto;
        }

        // Formatando o valor total para exibição
        $totalBaixas = number_format($valorTotal, 2, ',', '.');

        return $totalBaixas;
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
