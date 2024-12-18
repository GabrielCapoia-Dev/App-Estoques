<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Estoque;
use App\Models\EstoqueProduto;
use App\Models\Local;
use App\Models\Produto;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    /**
     * Chama a tela de relatórios passando os locais e as categorias
     */
    public function index(Request $request)
    {
        // Obter locais e categorias
        $locals = Local::all();
        $categorias = Categoria::all();
        $filtroRelatorioCategoria = null;
        $filtroRelatorioEstoque = null;
        $filtroRelatorioLocal = null;
        $filtroRelatorioPorLocalECategoria = null;
        $filtroRelatorioGeral = null;
        $filtroRelatorioGeralPorCategoria = null;


        // Retorna a view com os locais, categorias e produtos filtrados
        return view('relatorios.index', compact('locals', 'categorias', 'filtroRelatorioCategoria', 'filtroRelatorioEstoque', 'filtroRelatorioLocal', 'filtroRelatorioPorLocalECategoria', 'filtroRelatorioGeral', 'filtroRelatorioGeralPorCategoria'));
    }

    /**
     * Filtragem de produtos com base no form do relatório
     */
    public function filtroRelatorio(Request $request)
    {

        // Obter os filtros da requisição
        $localId = $request->input('local');
        $estoqueId = $request->input('estoque');
        $categoriaId = $request->input('categoria');
        $dataInicio = $request->input('dataInicio');
        $dataFim = $request->input('dataFim');

        if ($localId != null && $estoqueId != null && $categoriaId != null) {
            $filtro = $this->filtroRelatorioCategoria($localId, $estoqueId, $categoriaId, $dataInicio, $dataFim);

            return view('relatorios.index', [
                'locals' => Local::all(),
                'categorias' => Categoria::all(),
                'filtroRelatorioCategoria' => $filtro,
                'produtosFiltrados' => $filtro['produtosFiltrados'],
                'estoqueProdutosFiltrados' => $filtro['estoqueProdutosFiltrados'],
                'categoriaProdutosFiltrados' => $filtro['categoria'],
                'estoque' => $filtro['estoque'],
                'escola' => $filtro['local'],
            ]);
        }

        if ($localId != null && $estoqueId != null && $categoriaId == null) {
            $filtro = $this->filtroRelatorioEstoque($localId, $estoqueId, $dataInicio, $dataFim);
            // dd(json_encode($filtro, JSON_PRETTY_PRINT));
            return view('relatorios.index', [
                'locals' => Local::all(),
                'categorias' => Categoria::all(),
                'filtroRelatorioCategoria' => null,
                'filtroRelatorioEstoque' => $filtro,
                'produtosFiltrados' => $filtro['produtosFiltrados'],
                'estoqueProdutosFiltrados' => $filtro['estoqueProdutosFiltrados'],
                'estoque' => $filtro['estoque'],
                'escola' => $filtro['local'],
            ]);
        }

        if ($localId != null && $estoqueId == null && $categoriaId == null) {
            $filtro = $this->filtroRelatorioLocal($localId, $dataInicio, $dataFim);
            return view('relatorios.index', [
                'locals' => Local::all(),
                'categorias' => Categoria::all(),
                'filtroRelatorioCategoria' => null,
                'filtroRelatorioEstoque' => null,
                'filtroRelatorioLocal' => $filtro,
                'produtosFiltrados' => $filtro['produtosFiltrados'],
                'estoqueProdutosFiltrados' => $filtro['estoqueProdutosFiltrados'],
                'estoque' => $filtro['estoque'],
                'escola' => $filtro['local'],
            ]);
        }

        if (($localId != null && $categoriaId != null) && $estoqueId == null) {
            $filtro = $this->filtroRelatorioPorLocalECategoria($localId, $categoriaId, $dataInicio, $dataFim);
            return view('relatorios.index', [
                'locals' => Local::all(),
                'categorias' => Categoria::all(),
                'filtroRelatorioCategoria' => null,
                'filtroRelatorioEstoque' => null,
                'filtroRelatorioLocal' => null,
                'filtroRelatorioPorLocalECategoria' => $filtro,
                'produtosFiltrados' => $filtro['produtosFiltrados'],
                'estoqueProdutosFiltrados' => $filtro['estoqueProdutosFiltrados'],
                'estoque' => $filtro['estoque'],
                'escola' => $filtro['local'],
            ]);
        }

        if ($localId == null && $categoriaId == null && $estoqueId == null) {
            // dd('Geral');
            $filtro = $this->filtroRelatorioGeral($dataInicio, $dataFim);
            return view('relatorios.index', [
                'locals' => Local::all(),
                'categorias' => Categoria::all(),
                'filtroRelatorioCategoria' => null,
                'filtroRelatorioEstoque' => null,
                'filtroRelatorioLocal' => null,
                'filtroRelatorioPorLocalECategoria' => null,
                'filtroRelatorioGeral' => $filtro,
                'produtosFiltrados' => $filtro['produtosFiltrados'],
                'estoqueProdutosFiltrados' => $filtro['estoqueProdutosFiltrados'],
                'escola' => $filtro['local'],
            ]);
        }

        if ($localId == null && $categoriaId != null && $estoqueId == null) {
            $filtro = $this->filtroRelatorioGeralPorCategoria($categoriaId, $dataInicio, $dataFim);
            return view('relatorios.index', [
                'locals' => Local::all(),
                'categorias' => Categoria::all(),
                'filtroRelatorioCategoria' => null,
                'filtroRelatorioEstoque' => null,
                'filtroRelatorioLocal' => null,
                'filtroRelatorioPorLocalECategoria' => null,
                'filtroRelatorioGeral' => null,
                'filtroRelatorioGeralPorCategoria' => $filtro,
                'produtosFiltrados' => $filtro['produtosFiltrados'],
                'estoqueProdutosFiltrados' => $filtro['estoqueProdutosFiltrados'],
                'categoria' => $filtro['categoria'],
            ]);
        }
    }


    /**
     * Filtragem de produtos no estoque, com base na categoria solicitada
     */
    public function filtroRelatorioCategoria($localId, $estoqueId, $categoriaId, $dataInicio = null, $dataFim = null)
    {
        // Recupera o estoque e a categoria
        $local = Local::findOrFail($localId);
        $estoque = Estoque::findOrFail($estoqueId);
        $categoria = Categoria::findOrFail($categoriaId);

        // Constrói a consulta base para os produtos no estoque e na categoria especificada
        $query = Produto::whereHas('estoques', function ($query) use ($estoque) {
            $query->where('estoque_id', $estoque->id);
        })->where('id_categoria', $categoria->id);
        // Obtém os produtos filtrados
        $produtosFiltrados = $query->get();

        // Extrai os IDs dos produtos filtrados
        $produtosFiltradosIds = $produtosFiltrados->pluck('id')->toArray();

        // Realiza uma nova busca no estoque com base nos IDs dos produtos filtrados
        $estoqueProdutosFiltrados = EstoqueProduto::whereIn('produto_id', $produtosFiltradosIds)
            ->where('estoque_id', $estoque->id)
            ->where('quantidade_atual', '>', 0);

        // Filtragem por validade, se as datas forem fornecidas
        if ($dataInicio && $dataFim) {
            // Converte as datas de início e fim para o formato correto de validade
            $dataInicio = Carbon::createFromFormat('Y-m-d', $dataInicio)->startOfDay(); // Começo do dia
            $dataFim = Carbon::createFromFormat('Y-m-d', $dataFim)->endOfDay(); // Fim do dia

            // Aplica a filtragem por validade (usando `whereBetween` no campo `updated_at`)
            $estoqueProdutosFiltrados->whereBetween('updated_at', [$dataInicio, $dataFim]);
        } elseif ($dataInicio) {
            // Caso só tenha sido fornecida a data de início
            $dataInicio = Carbon::createFromFormat('Y-m-d', $dataInicio)->startOfDay(); // Começo do dia
            $estoqueProdutosFiltrados->where('updated_at', '>=', $dataInicio);
        } elseif ($dataFim) {
            // Caso só tenha sido fornecida a data de fim
            $dataFim = Carbon::createFromFormat('Y-m-d', $dataFim)->endOfDay(); // Fim do dia
            $estoqueProdutosFiltrados->where('updated_at', '<=', $dataFim);
        }

        // Obtém os estoque produtos filtrados
        $estoqueProdutosFiltrados = $estoqueProdutosFiltrados->get();

        // Verifica se a coleção está vazia
        if ($produtosFiltrados->isEmpty() && $estoqueProdutosFiltrados->isEmpty()) {
            return [
                'produtosFiltrados' => 'erro',
                'estoqueProdutosFiltrados' => 'erro',
                'categoria' => 'erro',
                'estoque' => 'erro',
                'local' => 'erro',
            ];
        }
        // Retorna os produtos filtrados e a categoria
        return [
            'produtosFiltrados' => $produtosFiltrados,
            'estoqueProdutosFiltrados' => $estoqueProdutosFiltrados,
            'categoria' => $categoria,
            'estoque' => $estoque,
            'local' => $local,
        ];
    }

    /**
     * Filtragem de produtos no estoque
     */
    public function filtroRelatorioEstoque($localId, $estoqueId, $dataInicio = null, $dataFim = null)
    {
        // Recupera o local e o estoque
        $local = Local::findOrFail($localId);
        $estoque = Estoque::findOrFail($estoqueId);

        // Constrói a consulta base para os produtos no estoque especificado
        $query = Produto::whereHas('estoques', function ($query) use ($estoque) {
            $query->where('estoque_id', $estoque->id);
        })->with('categoria'); // Carrega a relação com a categoria

        // Obtém os produtos filtrados
        $produtosFiltrados = $query->get();
        // Extrai os IDs dos produtos filtrados
        $produtosFiltradosIds = $produtosFiltrados->pluck('id')->toArray();

        // Realiza uma nova busca no estoque com base nos IDs dos produtos filtrados
        $estoqueProdutosFiltrados = EstoqueProduto::whereIn('produto_id', $produtosFiltradosIds)
            ->where('estoque_id', $estoque->id)
            ->where('quantidade_atual', '>', 0);

        // Filtragem por validade, se as datas forem fornecidas
        if ($dataInicio && $dataFim) {
            // Converte as datas de início e fim para o formato correto de validade
            $dataInicio = Carbon::createFromFormat('Y-m-d', $dataInicio)->startOfDay(); // Começo do dia
            $dataFim = Carbon::createFromFormat('Y-m-d', $dataFim)->endOfDay(); // Fim do dia

            // Aplica a filtragem por validade (usando `whereBetween` no campo `updated_at`)
            $estoqueProdutosFiltrados->whereBetween('updated_at', [$dataInicio, $dataFim]);
        } elseif ($dataInicio) {
            // Caso só tenha sido fornecida a data de início
            $dataInicio = Carbon::createFromFormat('Y-m-d', $dataInicio)->startOfDay(); // Começo do dia
            $estoqueProdutosFiltrados->where('updated_at', '>=', $dataInicio);
        } elseif ($dataFim) {
            // Caso só tenha sido fornecida a data de fim
            $dataFim = Carbon::createFromFormat('Y-m-d', $dataFim)->endOfDay(); // Fim do dia
            $estoqueProdutosFiltrados->where('updated_at', '<=', $dataFim);
        }

        // Obtém os estoque produtos filtrados
        $estoqueProdutosFiltrados = $estoqueProdutosFiltrados->get();

        // Verifica se a coleção está vazia
        if ($produtosFiltrados->isEmpty()) {
            return [
                'produtosFiltrados' => 'erro',
                'estoqueProdutosFiltrados' => 'erro',
                'estoque' => 'erro',
                'local' => 'erro',
            ];
        }
        // Retorna os produtos filtrados, suas categorias, o estoque e o local
        return [
            'produtosFiltrados' => $produtosFiltrados,
            'estoqueProdutosFiltrados' => $estoqueProdutosFiltrados,
            'estoque' => $estoque,
            'local' => $local,
        ];
    }


    /**
     * Filtragem de produtos no estoque
     */
    public function filtroRelatorioLocal($localId, $dataInicio = null, $dataFim = null)
    {
        // Recupera o local
        $local = Local::findOrFail($localId);

        // Obtém os IDs dos estoques associados ao local
        $estoquesIds = $local->estoques->pluck('id')->toArray();

        // Constrói a consulta base para os produtos nos estoques especificados
        $query = Produto::whereHas('estoques', function ($query) use ($estoquesIds) {
            $query->whereIn('estoque_id', $estoquesIds);
        });

        // Obtém os produtos filtrados
        $produtosFiltrados = $query->get();

        // Extrai os IDs dos produtos filtrados
        $produtosFiltradosIds = $produtosFiltrados->pluck('id')->toArray();

        // Realiza uma nova busca no estoque com base nos IDs dos produtos filtrados
        $estoqueProdutosFiltrados = EstoqueProduto::whereIn('produto_id', $produtosFiltradosIds)
            ->where('estoque_id', $estoquesIds)
            ->where('quantidade_atual', '>', 0);

        // Filtragem por validade, se as datas forem fornecidas
        if ($dataInicio && $dataFim) {
            // Converte as datas de início e fim para o formato correto de validade
            $dataInicio = Carbon::createFromFormat('Y-m-d', $dataInicio)->startOfDay(); // Começo do dia
            $dataFim = Carbon::createFromFormat('Y-m-d', $dataFim)->endOfDay(); // Fim do dia

            // Aplica a filtragem por validade (usando `whereBetween` no campo `updated_at`)
            $estoqueProdutosFiltrados->whereBetween('updated_at', [$dataInicio, $dataFim]);
        } elseif ($dataInicio) {
            // Caso só tenha sido fornecida a data de início
            $dataInicio = Carbon::createFromFormat('Y-m-d', $dataInicio)->startOfDay(); // Começo do dia
            $estoqueProdutosFiltrados->where('updated_at', '>=', $dataInicio);
        } elseif ($dataFim) {
            // Caso só tenha sido fornecida a data de fim
            $dataFim = Carbon::createFromFormat('Y-m-d', $dataFim)->endOfDay(); // Fim do dia
            $estoqueProdutosFiltrados->where('updated_at', '<=', $dataFim);
        }

        // Obtém os estoque produtos filtrados
        $estoqueProdutosFiltrados = $estoqueProdutosFiltrados->get();
        // Verifica se a coleção está vazia
        if ($produtosFiltrados->isEmpty()) {
            return [
                'produtosFiltrados' => 'erro',
                'estoqueProdutosFiltrados' => 'erro',
                'estoque' => 'erro',
                'local' => 'erro',
            ];
        }

        // Retorna os produtos filtrados, os estoques filtrados e o local
        return [
            'produtosFiltrados' => $produtosFiltrados,
            'estoqueProdutosFiltrados' => $estoqueProdutosFiltrados,
            'local' => $local,
            'estoque' => Estoque::where('id_local', $local->id)->get(),
        ];
    }

    /**
     * Filtragem de produtos em todos os estoques de um local, com base na categoria solicitada
     */
    public function filtroRelatorioPorLocalECategoria($localId, $categoriaId, $dataInicio = null, $dataFim = null)
    {
        // Recupera o local e a categoria
        $local = Local::findOrFail($localId);
        $categoria = Categoria::findOrFail($categoriaId);

        // Obtém os IDs dos estoques associados ao local
        $estoquesIds = $local->estoques->pluck('id')->toArray();
        $estoques = [];

        foreach ($estoquesIds as $id) {
            $estoque = Estoque::findOrFail($id);
            $estoques[] = $estoque;
        }

        // Constrói a consulta base para os produtos nos estoques especificados e na categoria fornecida
        $query = Produto::whereHas('estoques', function ($query) use ($estoquesIds) {
            $query->whereIn('estoque_id', $estoquesIds);
        })->where('id_categoria', $categoria->id);

        // Obtém os produtos filtrados
        $produtosFiltrados = $query->get();

        // Extrai os IDs dos produtos filtrados
        $produtosFiltradosIds = $produtosFiltrados->pluck('id')->toArray();


        // Realiza uma nova busca no estoque com base nos IDs dos produtos filtrados
        $estoqueProdutosFiltrados = EstoqueProduto::whereIn('produto_id', $produtosFiltradosIds)
            ->where('estoque_id', $estoquesIds)
            ->where('quantidade_atual', '>', 0);

        // Filtragem por validade, se as datas forem fornecidas
        if ($dataInicio && $dataFim) {
            // Converte as datas de início e fim para o formato correto de validade
            $dataInicio = Carbon::createFromFormat('Y-m-d', $dataInicio)->startOfDay(); // Começo do dia
            $dataFim = Carbon::createFromFormat('Y-m-d', $dataFim)->endOfDay(); // Fim do dia

            // Aplica a filtragem por validade (usando `whereBetween` no campo `updated_at`)
            $estoqueProdutosFiltrados->whereBetween('updated_at', [$dataInicio, $dataFim]);
        } elseif ($dataInicio) {
            // Caso só tenha sido fornecida a data de início
            $dataInicio = Carbon::createFromFormat('Y-m-d', $dataInicio)->startOfDay(); // Começo do dia
            $estoqueProdutosFiltrados->where('updated_at', '>=', $dataInicio);
        } elseif ($dataFim) {
            // Caso só tenha sido fornecida a data de fim
            $dataFim = Carbon::createFromFormat('Y-m-d', $dataFim)->endOfDay(); // Fim do dia
            $estoqueProdutosFiltrados->where('updated_at', '<=', $dataFim);
        }

        // Obtém os estoque produtos filtrados
        $estoqueProdutosFiltrados = $estoqueProdutosFiltrados->get();

        // Verifica se a coleção está vazia
        if ($produtosFiltrados->isEmpty()) {
            return [
                'produtosFiltrados' => 'erro',
                'estoqueProdutosFiltrados' => 'erro',
                'estoque' => 'erro',
                'local' => 'erro',
                'categoria' => 'erro',
            ];
        }

        // Retorna os produtos filtrados, os estoques e a categoria
        return [
            'produtosFiltrados' => $produtosFiltrados,
            'estoqueProdutosFiltrados' => $estoqueProdutosFiltrados,
            'local' => $local,
            'estoque' => $estoques,
            'categoria' => $categoria,
        ];
    }

    /**
     * Filtragem de todos os produtos de todos os estoques de todos os locais
     */
    public function filtroRelatorioGeral($dataInicio = null, $dataFim = null)
    {
        // Constrói a consulta base para os produtos em todos os estoques
        $query = Produto::whereHas('estoques');

        // Obtém todos os produtos filtrados
        $produtosFiltrados = $query->get();

        // Extrai os IDs dos produtos filtrados
        $produtosFiltradosIds = $produtosFiltrados->pluck('id')->toArray();


        // Realiza uma nova busca no estoque com base nos IDs dos produtos filtrados
        $estoqueProdutosFiltrados = EstoqueProduto::whereIn('produto_id', $produtosFiltradosIds)
            ->where('quantidade_atual', '>', 0);

        // Filtragem por validade, se as datas forem fornecidas
        if ($dataInicio && $dataFim) {
            // Converte as datas de início e fim para o formato correto de validade
            $dataInicio = Carbon::createFromFormat('Y-m-d', $dataInicio)->startOfDay(); // Começo do dia
            $dataFim = Carbon::createFromFormat('Y-m-d', $dataFim)->endOfDay(); // Fim do dia

            // Aplica a filtragem por validade (usando `whereBetween` no campo `updated_at`)
            $estoqueProdutosFiltrados->whereBetween('updated_at', [$dataInicio, $dataFim]);
        } elseif ($dataInicio) {
            // Caso só tenha sido fornecida a data de início
            $dataInicio = Carbon::createFromFormat('Y-m-d', $dataInicio)->startOfDay(); // Começo do dia
            $estoqueProdutosFiltrados->where('updated_at', '>=', $dataInicio);
        } elseif ($dataFim) {
            // Caso só tenha sido fornecida a data de fim
            $dataFim = Carbon::createFromFormat('Y-m-d', $dataFim)->endOfDay(); // Fim do dia
            $estoqueProdutosFiltrados->where('updated_at', '<=', $dataFim);
        }

        // Obtém os estoque produtos filtrados
        $estoqueProdutosFiltrados = $estoqueProdutosFiltrados->get();

        // Verifica se a coleção está vazia
        if ($produtosFiltrados->isEmpty()) {
            return [
                'produtosFiltrados' => 'erro',
                'estoqueProdutosFiltrados' => 'erro',
                'local' => 'erro',
            ];
        }

        // Retorna os produtos filtrados, os estoques e todos os locais com seus estoques
        return [
            'produtosFiltrados' => $produtosFiltrados,
            'estoqueProdutosFiltrados' => $estoqueProdutosFiltrados,
            'local' => Local::with('estoques')->get(),
        ];
    }


    /**
     * Filtragem de produtos de todos os estoques de todos os locais, com base na categoria especificada
     */
    public function filtroRelatorioGeralPorCategoria($categoriaId, $dataInicio = null, $dataFim = null)
    {
        // Recupera a categoria
        $categoria = Categoria::findOrFail($categoriaId);

        // Constrói a consulta base para os produtos da categoria especificada
        $query = Produto::where('id_categoria', $categoria->id);

        // Obtém os produtos filtrados
        $produtosFiltrados = $query->get();


        // Extrai os IDs dos produtos filtrados
        $produtosFiltradosIds = $produtosFiltrados->pluck('id')->toArray();

        // Realiza uma nova busca no estoque com base nos IDs dos produtos filtrados
        $estoqueProdutosFiltrados = EstoqueProduto::whereIn('produto_id', $produtosFiltradosIds)
            ->where('quantidade_atual', '>', 0);

        // Filtragem por validade, se as datas forem fornecidas
        if ($dataInicio && $dataFim) {
            // Converte as datas de início e fim para o formato correto de validade
            $dataInicio = Carbon::createFromFormat('Y-m-d', $dataInicio)->startOfDay(); // Começo do dia
            $dataFim = Carbon::createFromFormat('Y-m-d', $dataFim)->endOfDay(); // Fim do dia

            // Aplica a filtragem por validade (usando `whereBetween` no campo `updated_at`)
            $estoqueProdutosFiltrados->whereBetween('updated_at', [$dataInicio, $dataFim]);
        } elseif ($dataInicio) {
            // Caso só tenha sido fornecida a data de início
            $dataInicio = Carbon::createFromFormat('Y-m-d', $dataInicio)->startOfDay(); // Começo do dia
            $estoqueProdutosFiltrados->where('updated_at', '>=', $dataInicio);
        } elseif ($dataFim) {
            // Caso só tenha sido fornecida a data de fim
            $dataFim = Carbon::createFromFormat('Y-m-d', $dataFim)->endOfDay(); // Fim do dia
            $estoqueProdutosFiltrados->where('updated_at', '<=', $dataFim);
        }

        // Obtém os estoque produtos filtrados
        $estoqueProdutosFiltrados = $estoqueProdutosFiltrados->get();
        // Verifica se a coleção está vazia
        if ($produtosFiltrados->isEmpty()) {
            return [
                'produtosFiltrados' => 'erro',
                'estoqueProdutosFiltrados' => 'erro',
                'categoria' => 'erro',
            ];
        }

        // Retorna os produtos filtrados e os estoques
        return [
            'produtosFiltrados' => $produtosFiltrados,
            'estoqueProdutosFiltrados' => $estoqueProdutosFiltrados,
            'categoria' => $categoria,
        ];
    }
}
