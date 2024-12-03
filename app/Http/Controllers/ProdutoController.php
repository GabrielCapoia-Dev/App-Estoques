<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Estoque;
use App\Models\HistoricoProduto;
use App\Models\NotificacaoProduto;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdutoController extends Controller
{
    /**
     * Exibir todos os produtos
     */
    public function index()
    {
        $produtos = Produto::all();
        return view('produtos.index', compact('produtos'));
    }

    /**
     * Exibir o formulário para criar um novo produto
     */
    public function create()
    {
        $estoques = Estoque::all();
        $categorias = Categoria::all();
        return view('produtos.create', compact('estoques', 'categorias'));
    }

    /**
     * Criar um novo produto e salvar no histórico
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $validated = $request->validate([
            'nome_produto' => 'required|string|max:255',
            'id_categoria' => 'required|exists:categorias,id',
            'descricao_produto' => 'required|string',
            'preco' => 'nullable|string',
            'status_produto' => 'nullable|string',
        ]);

        // Criação do produto
        $produto = Produto::create(array_merge($validated, ['status_produto' => 'Ativo']));

        // Associando produto ao(s) estoque(s) com dados extras do pivot (quantidade e validade)
        if ($request->has('estoques')) {
            foreach ($request->estoques as $estoqueData) {
                $produto->estoques()->attach($estoqueData['estoque_id'], [
                    'quantidade_atual' => $estoqueData['quantidade_atual'],
                    'quantidade_minima' => $estoqueData['quantidade_minima'],
                    'quantidade_maxima' => $estoqueData['quantidade_maxima'],
                    'validade' => $estoqueData['validade'],
                ]);
            }
        }

        // Redirecionar para a página de produtos
        return redirect()->route('produtos.index')->with('success', 'Produto criado com sucesso!');
    }


    /**
     * Exibir detalhes de um produto específico
     */
    public function show($id)
    {
        $produto = Produto::findOrFail($id);
        return view('produtos.show', compact('produto'));
    }

    public function edit($id)
    {
        $produto = Produto::findOrFail($id);
        $categorias = Categoria::all();
        return view('produtos.edit', compact('produto', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        // Validação
        $validated = $request->validate([
            'nome_produto' => 'required|string|max:255',
            'id_categoria' => 'required|exists:categorias,id',
            'descricao_produto' => 'required|string',
            'preco' => 'nullable|numeric',
        ]);

        // Atualiza o produto
        $produto = Produto::findOrFail($id);
        $produto->update($validated);

        return redirect()->route('produtos.index')->with('success', 'Produto atualizado com sucesso!');
    }
}
