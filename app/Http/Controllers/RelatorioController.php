<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Estoque;
use App\Models\EstoqueProduto;
use App\Models\Local;
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


        // Retorna a view com os locais, categorias e produtos filtrados
        return view('relatorios.index', compact('locals', 'categorias'));
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

        // Iniciar a consulta base com o modelo EstoqueProduto
        $query = EstoqueProduto::query();

        // Filtrar por local se fornecido
        if ($localId) {
            $query->whereHas('estoque', function ($q) use ($localId) {
                $q->where('id_local', $localId);
            });
        }

        // Filtrar por estoque se fornecido
        if ($estoqueId) {
            $query->where('estoque_id', $estoqueId);
        }

        // Filtrar por categoria se fornecido
        if ($categoriaId) {
            $query->whereHas('produto', function ($q) use ($categoriaId) {
                $q->where('id_categoria', $categoriaId);
            });
        }

        // Filtrar por data de início e fim se fornecido
        if ($dataInicio && $dataFim) {
            $query->whereBetween('created_at', [$dataInicio, $dataFim]);
        }

        // Executar a consulta e pegar os resultados
        $produtosEstoque = $query->get();

        // Retornar a view com os resultados filtrados
        return view('relatorios.index', compact('produtosEstoque'));
    }
}
