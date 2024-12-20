<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\DescarteProdutos;
use App\Models\Estoque;
use App\Models\EstoqueProduto;
use App\Models\Local;
use App\Models\Produto;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Response;

class DownloadController extends Controller
{

    /**
     * Gerar download do relatório chamando o metodo de conversão
     * no formato do arquivo
     */
    public function downloadBaixas(Request $request)
    {

        $valorTotalBaixaGeral = 0;
        $valorTotalEstoqueGeral = 0;
        $resultado = [];
        $estoqueController = new EstoqueController();


        $requestValorTodasEscolas = $request->has('todos');
        $requestEscolas = $request->input('escolas');
        $requestFormato = $request->has('formato');
        $requestDataInicio = $request->input('data-inicio');
        $requestDataFim = $request->input('data-fim');
        $requestMotivoDescarte = $request->input('motivo-descarte');
        $requestCategoria = $request->input('categoria-select');

        //Se não foi passado o valor para todas as escolas e se foi passado o valor de apenas UMA escola então:
        if ($requestValorTodasEscolas == false && $requestEscolas != null) {
            if (is_array($requestEscolas) && count($requestEscolas) === 1) {
                $idEscola = $requestEscolas[0];
            } elseif (!is_array($requestEscolas)) {
                $idEscola = $requestEscolas;
            } else {
                return redirect()->back()->with('error', 'Selecione apenas uma escola ou todas as escolas.');
            }

            if (!Local::where('id', $idEscola)->exists()) {
                return redirect()->back()->with('error', 'A escola selecionada não existe.');
            }

            return $this->downloadIndividualCsv($idEscola, $requestDataFim, $requestDataInicio, $requestMotivoDescarte, $requestCategoria);
        }


        //Se foi passado o valor para TODAS as escolas então:
        if ($requestValorTodasEscolas == true) {
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

            if ($requestFormato && in_array('csv', $request->input('formato'))) {
                return $this->downloadCsvTodos($resultado);
            }
        }
        return redirect()->back()->with('error', 'Selecione um formato de download.');
    }


    /**
     * Gera e faz o download do relatório de baixas em formato CSV.
     */
    public function downloadCsvTodos($resultado)
    {
        $csvContent = "Local;Valor Total Estoque;Valor Total Baixa\n";

        $totalEstoqueGeral = 0;
        $totalBaixaGeral = 0;

        foreach ($resultado as $row) {
            $valorEstoque = $row['valorTotalEstoque'];
            $valorBaixa = $row['valorTotalBaixa'];

            $totalEstoqueGeral += $valorEstoque;
            $totalBaixaGeral += $valorBaixa;

            $csvContent .= implode(';', [
                $row['local'],
                'R$ ' . number_format($valorEstoque, 2, ',', '.'),
                'R$ ' . number_format($valorBaixa, 2, ',', '.')
            ]) . "\n";
        }

        $csvContent .= implode(';', [
            'TOTAL',
            'R$ ' . number_format($totalEstoqueGeral, 2, ',', '.'),
            'R$ ' . number_format($totalBaixaGeral, 2, ',', '.')
        ]) . "\n";

        $nomeArquivo = 'relatorio-todas-as-escolas.csv';

        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"',
        ]);
    }

    /**
     * Gera e faz o download do relatório de baixas em formato CSV de uma escola individualmente
     */
    public function downloadIndividualCsv($idEscola, $requestDataFim, $requestDataInicio, $requestMotivoDescarte, $requestCategoria)
    {
        if (!$idEscola) {
            return redirect()->back()->with('error', 'Escola não encontrada.');
        }

        // Obter todos os estoques da escola
        $estoquesEscola = Estoque::where('id_local', $idEscola)->get();

        $escola = Local::findOrFail($idEscola);

        // Cabeçalho para o CSV
        $header = [
            'id_baixa',
            'id_escola',
            'nome_escola',
            'id_estoque',
            'nome_estoque',
            'id_produto',
            'nome_produto',
            'id_categoria',
            'nome_categoria',
            'valor_produto',
            'quantidade_descarte',
            'motivo_descarte',
            'validade',
            'data_descarte',
            'valor_total'
        ];

        // Inicializar o conteúdo do CSV
        $csvContent = implode(';', $header) . "\n";

        $descarte = new DescarteProdutoController();

        // Variáveis para somar os totais
        $totalEstoqueGeral = 0;

        // Iterar sobre os estoques da escola
        foreach ($estoquesEscola as $estoque) {
            // Obter as baixas para cada estoque
            $baixas = $descarte->show($estoque->id, $requestDataInicio, $requestDataFim);

            foreach ($baixas['dadosCompletos'] as $item) {
                $estoqueProduto = $item['baixas'];
                if ($estoqueProduto['id_categoria'] == $requestCategoria || $requestCategoria == null) {
                    if (($estoqueProduto['defeito_descarte'] == $requestMotivoDescarte) || ($requestMotivoDescarte == null)) {
                        $produto = $item['produtos'];
                        $produtoCategoria = Produto::find($produto['id_produto']);
                        $categoria = Categoria::find($produtoCategoria['id_categoria']);

                        $precoProduto = floatval(str_replace(',', '.', str_replace('.', '', $produto['preco_produto'])));
                        $quantidadeDescarte = floatval($estoqueProduto['quantidade_descarte']);

                        // Calcular o valor do item (valor unitário * quantidade)
                        $valorItem = $precoProduto * $quantidadeDescarte;

                        // Somar aos totais
                        $totalEstoqueGeral += $precoProduto * $quantidadeDescarte;

                        // Adicionar a linha para o CSV
                        $csvContent .= implode(';', [
                            $estoqueProduto['id'], // id da baixa
                            $escola->id, // id da escola
                            $escola->nome_local, // nome da escola
                            $estoque->id, // id do estoque
                            $estoque->nome_estoque, // nome do estoque
                            $produto['id_produto'], // id do produto
                            $estoqueProduto['nome_produto'], // nome do produto
                            $categoria->id, // id da categoria
                            $categoria->nome_categoria, // nome da categoria
                            number_format($precoProduto, 2, ',', '.'), // valor do produto
                            number_format($estoqueProduto['quantidade_descarte'], 2, ',', '.'), // quantidade descartada
                            $estoqueProduto['defeito_descarte'], // defeito descartado
                            $estoqueProduto['validade'], // validade
                            $estoqueProduto['created_at'], // data de descarte
                            number_format($valorItem, 2, ',', '.') // valor do item (valor unitário * quantidade)
                        ]) . "\n";
                    }
                }
            }
        }

        // Adicionar a linha de totais ao final
        $csvContent .= implode(';', [
            'TOTAL',
            'R$ ' . number_format($totalEstoqueGeral, 2, ',', '.'),
        ]) . "\n";

        // Nome do arquivo
        $nomeArquivo = 'relatorio_baixas_estoques_escola_' . $escola->nome_local . '.csv';

        // Retornar o arquivo CSV como resposta
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"',
        ]);
    }

    /**
     * Gera e faz o download do relatório de baixas em formato CSV de uma escola individualmente
     */
    public function downloadIndividualDoEstoque($idEscola, $idEstoque)
    {
        if (!$idEscola) {
            return redirect()->back()->with('error', 'Escola não encontrada.');
        }

        // Obter todos os estoques da escola
        $estoqueEscola = Local::findOrFail($idEstoque);

        $escola = Local::findOrFail($idEscola);

        // Cabeçalho para o CSV
        $header = [
            'id_baixa',
            'id_escola',
            'nome_escola',
            'id_estoque',
            'nome_estoque',
            'id_produto',
            'nome_produto',
            'valor_produto',
            'quantidade_descarte',
            'motivo_descarte',
            'validade',
            'data_descarte',
            'valor_total'
        ];

        // Inicializar o conteúdo do CSV
        $csvContent = implode(';', $header) . "\n";

        $descarte = new DescarteProdutoController();

        // Variáveis para somar os totais
        $totalEstoqueGeral = 0;

        // Obter as baixas para cada estoque
        $baixas = $descarte->show($estoqueEscola->id); // Chama a função show de DescarteProdutosController

        foreach ($baixas['dadosCompletos'] as $item) {
            $estoqueProduto = $item['baixas'];
            $produto = $item['produtos'];

            $precoProduto = floatval(str_replace(',', '.', str_replace('.', '', $produto['preco_produto'])));
            $quantidadeDescarte = floatval($estoqueProduto['quantidade_descarte']);

            // Calcular o valor do item (valor unitário * quantidade)
            $valorItem = $precoProduto * $quantidadeDescarte;

            // Somar aos totais
            $totalEstoqueGeral += $precoProduto * $quantidadeDescarte;

            // Adicionar a linha para o CSV
            $csvContent .= implode(';', [
                $estoqueProduto['id'], // id da baixa
                $escola->id, // id da escola
                $escola->nome_local, // nome da escola
                $estoqueEscola->id, // id do estoque
                $estoqueEscola->nome_estoque, // nome do estoque
                $produto['id_produto'], // id do produto
                $estoqueProduto['nome_produto'], // nome do produto
                number_format($precoProduto, 2, ',', '.'), // valor do produto
                number_format($estoqueProduto['quantidade_descarte'], 2, ',', '.'), // quantidade descartada
                $estoqueProduto['defeito_descarte'], // defeito descartado
                $estoqueProduto['validade'], // validade
                $estoqueProduto['created_at'], // data de descarte
                number_format($valorItem, 2, ',', '.') // valor do item (valor unitário * quantidade)
            ]) . "\n";
        }

        // Adicionar a linha de totais ao final
        $csvContent .= implode(';', [
            'TOTAL',
            'R$ ' . number_format($totalEstoqueGeral, 2, ',', '.'),
        ]) . "\n";

        // Nome do arquivo
        $nomeArquivo = 'relatorio_baixas_estoques_escola_' . $escola->nome_local . '.csv';

        // Retornar o arquivo CSV como resposta
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"',
        ]);
    }

    /**
     * Gera e faz o download do relatório de baixas em formato PDF
     */
    public function downloadPdf($resultado, $todos) {}


    public function downloadRelatoriosBaixas(Request $request)
    {
        $baixaForm = $request->input('baixa');

        if ($baixaForm == 'true') {

            // Chamar o método para filtrar os dados
            $filtroRelatorio = new RelatorioController();
            $resultadoFiltro = $filtroRelatorio->filtroRelatorio($request);

            // dd($resultadoFiltro);

            // Verificar se o formato é CSV
            if ($request->formato == 'csv') {
                // Cabeçalho do CSV
                $header = [
                    'ID Baixa',
                    'ID Escola',
                    'Nome Escola',
                    'ID Estoque',
                    'Nome Estoque',
                    'ID Produto',
                    'Nome Produto',
                    'ID Categoria',
                    'Nome Categoria',
                    'Valor Produto',
                    'Quantidade Descarte',
                    'Motivo Descarte',
                    'Validade',
                    'Atualizado em',
                    'Valor Total'
                ];

                // Inicializar o conteúdo do CSV
                $csvContent = implode(';', $header) . "\n";

                // Variáveis para totais
                $totalValor = 0;

                // Iterar sobre os produtos filtrados
                foreach ($resultadoFiltro['estoqueProdutosFiltrados'] as $estoqueProduto) {
                    foreach ($estoqueProduto->descartes as $descarte) {
                        // Verificar se o descarte está preenchido
                        if (!empty($descarte)) {
                            // Obter a quantidade do produto
                            $quantidade = (int) $descarte->quantidade_descarte;
                            
                            // Obter o preço do produto
                            $preco = (float) str_replace(',', '.', $estoqueProduto->produto->preco);
                            
                            // Calcular o valor total do item
                            $valorItem = $preco * $quantidade;
                            
                            // Somar ao valor total
                            $totalValor += $valorItem;

                            // Adicionar os dados no CSV
                            $csvContent .= implode(';', [
                                $estoqueProduto->id ?? 'N/A',
                                $estoqueProduto->estoque->local->id ?? 'N/A',
                                $estoqueProduto->estoque->local->nome_local ?? 'Local não disponível',
                                $estoqueProduto->estoque->id ?? 'N/A',
                                $estoqueProduto->estoque->nome_estoque ?? 'Estoque não disponível',
                                $estoqueProduto->produto->id ?? 'N/A',
                                $estoqueProduto->produto->nome_produto ?? 'Produto não disponível',
                                $estoqueProduto->produto->categoria->id ?? 'N/A',
                                $estoqueProduto->produto->categoria->nome_categoria ?? 'Categoria não disponível',
                                number_format($preco, 2, ',', '.'),
                                $quantidade ?? 'N/A',
                                $descarte->defeito_descarte ?? 'Sem descarte',
                                $estoqueProduto->validade ? \Carbon\Carbon::parse($estoqueProduto->validade)->format('d-m-Y') : 'Sem validade',
                                $estoqueProduto->updated_at ? \Carbon\Carbon::parse($estoqueProduto->updated_at)->format('d-m-Y') : 'Sem data de atualização',
                                number_format($valorItem, 2, ',', '.')
                            ]) . "\n";
                        }
                    }
                }

                // Adicionar linha de totais
                $csvContent .= implode(';', [
                    'TOTAL',
                    number_format($totalValor, 2, ',', '.')
                ]) . "\n";

                $timezone = new DateTimeZone('America/Sao_Paulo');
                $data = new DateTime('now', $timezone);
                $nomeArquivo = 'relatorio_' . $data->format('d-m-Y') . '_baixas_estoques.csv';

                // Retornar o arquivo CSV para download
                return response()->make($csvContent, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"',
                ]);
            }

            // Caso o formato não seja CSV, outras lógicas podem ser implementadas
        }
    }


    public function downloadRelatorios(Request $request)
    {

        // Chamar o método para filtrar os dados
        $filtroRelatorio = new RelatorioController();
        $resultadoFiltro = $filtroRelatorio->filtroRelatorio($request);
        // Verificar se o formato é CSV
        if ($request->formato == 'csv') {
            // Cabeçalho do CSV
            $header = [
                'ID Escola',
                'Nome Escola',
                'ID Estoque',
                'Nome Estoque',
                'ID Produto',
                'Nome Produto',
                'ID Categoria',
                'Nome Categoria',
                'Valor Produto',
                'Quantidade Descartada',
                'Validade',
                'Atualizado em',
                'Valor Total'
            ];

            // Inicializar o conteúdo do CSV
            $csvContent = implode(';', $header) . "\n";

            // Variáveis para totais
            $totalValor = 0;

            // Iterar sobre os produtos filtrados
            foreach ($resultadoFiltro['estoqueProdutosFiltrados'] as $estoqueProduto) {

                // Obter a quantidade do produto
                $quantidade = (float) $estoqueProduto->quantidade_atual;

                // Verificar se a quantidade é maior que 0
                if ($quantidade > 0) {

                    // Obter o preço do produto
                    $preco = (float) str_replace(',', '.', $estoqueProduto->produto->preco);

                    // Calcular o valor total do item
                    $valorItem = $preco * $quantidade;

                    // Somar ao valor total
                    $totalValor += $valorItem;

                    // Adicionar os dados no CSV
                    $csvContent .= implode(';', [
                        $estoqueProduto->estoque->local->id ?? 'N/A',
                        $estoqueProduto->estoque->local->nome_local ?? 'Local não disponível',
                        $estoqueProduto->estoque->id ?? 'N/A',
                        $estoqueProduto->estoque->nome_estoque ?? 'Estoque não disponível',
                        $estoqueProduto->id ?? 'N/A',
                        $estoqueProduto->produto->nome_produto ?? 'Produto não disponível',
                        $estoqueProduto->produto->categoria->id ?? 'N/A',
                        $estoqueProduto->produto->categoria->nome_categoria ?? 'Categoria não disponível',
                        number_format($preco, 2, ',', '.'),
                        number_format($quantidade, 0, ',', '.'),
                        $estoqueProduto->validade ? \Carbon\Carbon::parse($estoqueProduto->validade)->format('d-m-Y') : 'Sem validade',
                        $estoqueProduto->updated_at ? \Carbon\Carbon::parse($estoqueProduto->updated_at)->format('d-m-Y') : 'Sem data de atualização',
                        number_format($valorItem, 2, ',', '.')
                    ]) . "\n";
                }
            }

            // Adicionar linha de totais
            $csvContent .= implode(';', [
                'TOTAL',
                number_format($totalValor, 2, ',', '.')
            ]) . "\n";

            $timezone = new DateTimeZone('America/Sao_Paulo');
            $data = new DateTime('now', $timezone);
            $nomeArquivo = 'relatorio_' . $data->format('d-m-Y') . '_estoques.csv';

            // Retornar o arquivo CSV para download
            return response()->make($csvContent, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"',
            ]);
        }

        // Caso o formato não seja CSV, outras lógicas podem ser implementadas
    }
}
