<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\EstoqueProduto;
use App\Models\DescarteProdutos;
use App\Models\Estoque;
use App\Models\Local;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use NunoMaduro\Collision\Writer;
use SplTempFileObject;

class DescarteProdutoController extends Controller
{

    /**
     * Lista todos os valores de todas as escolas.
     */
    public function index()
    {

        $categorias = Categoria::all();

        $valorTotalBaixaGeral = 0;
        $valorTotalEstoqueGeral = 0;


        $resultado = [];
        $estoqueController = new EstoqueController();
        $locals = Local::all();

        foreach ($locals as $local) {

            $requestMock = Request::create('', 'GET', ['status_estoque' => 'Ativo']);

            $totalEstoque = floatval(str_replace(',', '.', str_replace('.', '', $estoqueController->index($requestMock, $local->id)['totalEstoque'] ?? '0')));
            $totalBaixas = floatval(str_replace(',', '.', str_replace('.', '', $estoqueController->index($requestMock, $local->id)['totalBaixa'] ?? '0')));


            $valorTotalBaixaGeral += $totalBaixas;
            $valorTotalEstoqueGeral += $totalEstoque;

            $resultado[] = [
                'idLocal' => $local->id,
                'local' => $local->nome_local,
                'valorTotalEstoque' => $totalEstoque,
                'valorTotalBaixa' => $totalBaixas,
            ];
        }

        // Formatação dos totais gerais
        $totalBaixaGeralFormatado = number_format($valorTotalBaixaGeral, 2, ',', '.');

        $totalEstoqueGeralFormatado = number_format($valorTotalEstoqueGeral, 2, ',', '.');

        // Retorna a view com os dados
        return view('baixas.index', compact('locals', 'categorias', 'resultado', 'totalBaixaGeralFormatado', 'totalEstoqueGeralFormatado'));
    }



    /**
     * Exibir todas as baixas de um estoque
     */
    public function show($estoqueId, $dataInicio = null, $dataFim = null)
    {
        // Carregando o estoque e os produtos relacionados
        $estoque = Estoque::with(['produtos' => function ($query) {
            $query->wherePivot('quantidade_atual', '>', 0)
                ->withPivot('id', 'quantidade_atual', 'quantidade_minima', 'quantidade_maxima', 'validade');
        }])->findOrFail($estoqueId);
    
        // Carregando os dados relacionados ao estoque
        $estoque_produto = EstoqueProduto::where("estoque_id", $estoqueId)->get();
        $estoqueProdutoIds = $estoque_produto->pluck('id');
        $produtosIds = $estoque_produto->pluck('produto_id');
        $produtos = Produto::whereIn('id', $produtosIds)->get();
    
        // Carregando as baixas com o filtro de data, caso as datas sejam passadas
        $baixasQuery = DescarteProdutos::with('produto')
            ->whereIn('id_estoque_produto', $estoqueProdutoIds)
            ->orderBy('created_at', 'desc');
    
        // Aplica o filtro de data se as datas de início e fim forem fornecidas
        if ($dataInicio && $dataFim) {
            $baixasQuery->whereBetween('updated_at', [$dataInicio, $dataFim]);
        }
    
        $baixas = $baixasQuery->get();
    
        // Mapeando os dados completos das baixas e produtos
        $dadosCompletos = $baixas->map(function ($baixa) use ($produtos, $estoque_produto) {
            $estoqueProduto = $estoque_produto->firstWhere('id', $baixa->id_estoque_produto);
            $produto = $produtos->firstWhere('id', $estoqueProduto->produto_id);
    
            return [
                'baixas' => [
                    'id' => $baixa->id,
                    'id_estoque_produto' => $estoqueProduto->id,
                    'id_categoria' => $produto->id_categoria,
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
    
        // Calculando o valor total das baixas
        $totalBaixas = $this->calcularValorTotal($dadosCompletos->toArray());
    
        // Obtendo o id da escola
        $escola = $estoque['id'];
    
        // Retornando a view com os dados
        return view('baixas.show', compact('dadosCompletos', 'estoque', 'totalBaixas', 'escola'));
    }
    

    /**
     * Filtrar baixas do estoque
     */
    public function filtrarBaixas(Request $request, $estoqueId)
    {
        $estoque = Estoque::find($estoqueId);

        if (!$estoque) {
            return redirect()->route('estoques.index')->with('error', 'Estoque não encontrado.');
        }

        $request->validate([
            'defeito_descarte' => 'nullable|string',
            'validade-Inicio' => 'nullable|date',
            'validade-Fim' => 'nullable|date|after_or_equal:validade-Inicio',
        ]);

        $allData = $this->show($estoqueId, true);

        $dadosFiltrados = collect($allData['dadosCompletos']);

        if ($request->filled('defeito_descarte')) {
            $dadosFiltrados = $dadosFiltrados->filter(function ($item) use ($request) {
                return stripos($item['baixas']['defeito_descarte'], $request->input('defeito_descarte')) !== false;
            });
        }

        if ($request->filled('validade-Inicio')) {
            $dadosFiltrados = $dadosFiltrados->filter(function ($item) use ($request) {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $item['baixas']['validade']) >= \Carbon\Carbon::createFromFormat('Y-m-d', $request->input('validade-Inicio'));
            });
        }

        if ($request->filled('validade-Fim')) {
            $dadosFiltrados = $dadosFiltrados->filter(function ($item) use ($request) {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $item['baixas']['validade']) <= \Carbon\Carbon::createFromFormat('Y-m-d', $request->input('validade-Fim'));
            });
        }
        $escola = $estoque['id'];
        $totalBaixas = $this->calcularValorTotal($dadosFiltrados->toArray());

        return view('baixas.show', [
            'dadosCompletos' => $dadosFiltrados,
            'estoque' => $allData['estoque'],
            'totalBaixas' => $totalBaixas,
            'escola' => $escola
        ]);
    }

    /**
     * Calcular valor total de descarte
     */
    public function calcularValorTotal(array $dadosCompletos)
    {
        $valorTotal = 0;

        foreach ($dadosCompletos as $dado) {
            $quantidadeDescarte = floatval($dado['produtos']['quantidade_descarte']);

            $precoProduto = floatval(str_replace(',', '.', $dado['produtos']['preco_produto']));
            $valorTotal += $quantidadeDescarte * $precoProduto;
        }

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
