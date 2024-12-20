<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\EstoqueProduto;
use App\Models\Pedido;
use App\Models\Local;
use App\Models\EstoqueProdutoPedido;
use App\Models\Produto;
use Illuminate\Http\Request;

use function GuzzleHttp\json_decode;

class PedidoController extends Controller
{

    public function index(Request $request)
    {
        $pedidos = Pedido::query()
            ->when($request->filled('local'), function ($query) use ($request) {
                $query->where('id_local', $request->local);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('dataInicio'), function ($query) use ($request) {
                $query->where('data_prevista', '>=', $request->dataInicio);
            })
            ->when($request->filled('dataFim'), function ($query) use ($request) {
                $query->where('data_prevista', '<=', $request->dataFim);
            })
            ->paginate(10);  // Exemplo de 10 itens por página

        $locals = Local::all();

        return view('pedidos.index', compact('pedidos', 'locals'));
    }


    public function create()
    {
        $locals = Local::all();  // Listar locais
        $estoques = [];  // Inicializa um array vazio para os estoques

        // Verifica se há um local selecionado, se sim, carrega os estoques do local
        if (request()->has('local_id')) {
            $local = Local::find(request('local_id'));  // Encontrar o local pelo ID
            if ($local) {
                $estoques = $local->estoques;  // Pega os estoques do local
            }
        }

        $produtos = Produto::all();  // Listar produtos disponíveis

        return view('pedidos.create', compact('locals', 'estoques', 'produtos'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'local_id' => 'required|exists:locals,id',
            'estoque_id' => 'required|exists:estoques,id',
            'produtos' => 'required|array|min:1',
            'produtos.*.produto_id' => 'required|exists:produtos,id',
            'produtos.*.quantidade' => 'required|integer|min:1',
        ]);

        $pedido = Pedido::create([
            'local_id' => $request->local_id,
            'estoque_id' => $request->estoque_id,
            'status' => 'pendente',  // Status inicial do pedido
            'data_prevista' => now()->addDays(7),  // Exemplo de data prevista
        ]);

        // Adicionar produtos ao pedido
        foreach ($request->produtos as $produtoData) {
            $pedido->produtos()->attach($produtoData['produto_id'], ['quantidade' => $produtoData['quantidade']]);
        }

        return redirect()->route('pedidos.index')->with('success', 'Pedido criado com sucesso!');
    }


    public function edit(Pedido $pedido)
    {
        $locals = Local::all();
        $estoqueProdutosPedidos = EstoqueProdutoPedido::with('estoqueProduto')->get();

        return view('pedidos.edit', compact('pedido', 'locals', 'estoqueProdutosPedidos'));
    }


    public function update(Request $request, Pedido $pedido)
    {
        $request->validate([
            'id_local' => 'required|exists:locals,id',
            'estoque_produto_pedido_id' => 'required|exists:estoque_produto_pedido,id',
        ]);

        $pedido->update([
            'id_local' => $request->id_local,
            'estoque_produto_pedido_id' => $request->estoque_produto_pedido_id,
        ]);

        return redirect()->route('pedidos.index')->with('success', 'Pedido atualizado com sucesso.');
    }

    public function filtrarPedidos(Request $request)
    {

        $localId = $request->input('local');
        $estoqueId = $request->input('estoque');



        if ($localId == null && $estoqueId == null) {
            $filtro = $this->filtrarPedidosGeral($estoqueId);
        }
        if ($localId != null && $estoqueId == null) {
            $filtro = $this->filtrarPedidosLocal($localId);
        }
        if ($localId != null && $estoqueId != null) {
            $filtro = $this->filtrarPedidosEstoque($estoqueId);
        }
    }

    public function filtrarPedidosGeral($estoqueId) {}

    public function filtrarPedidosLocal($localId)
    {

        // Recupera o local e seus estoques
        $local = Local::findOrFail($localId);
        $estoques = Estoque::where('id_local', $localId)->get();

        // Obtém os IDs dos estoques
        $estoqueIds = $estoques->pluck('id');

        // Recupera os produtos nos estoques do local
        $estoqueProdutosFiltrados = EstoqueProduto::whereIn('estoque_id', $estoqueIds)
            ->with('produto', 'estoque')
            ->get();

        // Agrupa os produtos por ID e calcula a quantidade total no local
        $estoqueProdutosAgrupados = $estoqueProdutosFiltrados->groupBy('produto_id')->map(function ($produtos) {
            $produtoRelacionado = $produtos->first()->produto;
            $estoqueRelacionado = $produtos->first()->estoque;
            $localRelacionado = Local::find($estoqueRelacionado->id_local);

            return [
                'quantidade_atual' => $produtos->sum('quantidade_atual'),
                'quantidade_minima' => (int) $produtos->first()->quantidade_minima,
                'quantidade_maxima' => (int) $produtos->first()->quantidade_maxima,
                'produto' => json_decode($produtoRelacionado, JSON_OBJECT_AS_ARRAY),
                'estoque' => json_decode($estoqueRelacionado, JSON_OBJECT_AS_ARRAY),
                'local' => json_decode($localRelacionado, JSON_OBJECT_AS_ARRAY),
            ];
        });

        // Filtra os produtos abaixo da quantidade mínima
        $produtosAbaixoQuantidadeMinima = $estoqueProdutosAgrupados->filter(function ($produto) {
            return $produto['quantidade_atual'] <= $produto['quantidade_minima'];
        });
        dd([
            'Produtos Abaixo Quantidade Minima' => json_decode($produtosAbaixoQuantidadeMinima, JSON_OBJECT_AS_ARRAY),
        ]);
        // Retorna os resultados
        return [
            'produtos_abaixo_quantidade_minima' => $produtosAbaixoQuantidadeMinima->values(),
        ];
    }


    public function filtrarPedidosEstoque($estoqueId)
    {
        $estoque = Estoque::findOrFail($estoqueId);

        $query = Produto::whereHas('estoques', function ($query) use ($estoque) {
            $query->where('estoque_id', $estoque->id);
        })->with('categoria');

        $produtosFiltrados = $query->get();

        $produtosFiltradosIds = $produtosFiltrados->pluck('id')->toArray();

        $estoqueProdutosFiltrados = EstoqueProduto::whereIn('produto_id', $produtosFiltradosIds)
            ->where('estoque_id', $estoque->id)
            ->with('descartes')
            ->with('estoque')
            ->with('produto')
            ->get();

        $produtosAbaixoQuantidadeMinima = [];

        $estoqueProdutosAgrupados = $estoqueProdutosFiltrados->groupBy('produto_id')->map(function ($produtos) {
            $produtoRelacionado = $produtos->first()->produto;
            $estoqueRelacionado = $produtos->first()->estoque;
            $localRelacionado = Local::find($estoqueRelacionado->id_local);

            return [
                'quantidade_atual' => $produtos->sum('quantidade_atual'),
                'quantidade_minima' => (int) $produtos->first()->quantidade_minima,
                'quantidade_maxima' => (int) $produtos->first()->quantidade_maxima,
                'produto' => json_decode($produtoRelacionado, JSON_OBJECT_AS_ARRAY),
                'estoque' => json_decode($estoqueRelacionado, JSON_OBJECT_AS_ARRAY),
                'local' => json_decode($localRelacionado, JSON_OBJECT_AS_ARRAY),
            ];
        });


        foreach ($estoqueProdutosAgrupados as $produtoAgrupado) {

            $quantidade_minima = $produtoAgrupado['quantidade_minima'];
            $quantidade_atual = $produtoAgrupado['quantidade_atual'];

            if ($quantidade_atual <= $quantidade_minima) {

                $produtosAbaixoQuantidadeMinima[] = $produtoAgrupado;
            }
        }


        dd([
            'Produtos Abaixo da Quantidade Mínima' => $produtosAbaixoQuantidadeMinima,
        ]);
        return ($produtosAbaixoQuantidadeMinima);
    }
}
