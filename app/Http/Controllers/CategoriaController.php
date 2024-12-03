<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{
    /**
     * Listar todas as categorias
     */
    public function index()
    {
        $categorias = Categoria::all();

        return view('categorias.index', compact('categorias'));
    }

    /**
     * Retornar a view com o formulário de criação
     */
    public function create()
    {
        return view('categorias.create');
    }


    /**
     * Criar uma caregoria especifica
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nome_categoria' => 'required|string|min:2|max:30',
                'descricao_categoria' => 'required|string|min:2|max:255',
                'status_categoria' => 'required|in:Ativo,Inativo',
            ],
            [
                'required' => 'O campo :attribute é obrigatório.',
                'exists' => 'O campo :attribute nao existe.',
                'string' => 'O campo :attribute deve ser uma string.',
                'in' => 'O campo :attribute deve ser "Ativo" ou "Inativo".',
                'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
                'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            ],
            [
                'nome_categoria' => 'Nome da categoria',
                'descricao_categoria' => 'Descrição da categoria',
                'status_categoria' => 'Status da categoria',
            ]
        );

        if ($validator->fails()) {
            return redirect()->route('categorias.create')
                ->withErrors($validator)
                ->withInput();
        }

        Categoria::create($request->all());

        return redirect()->route('categorias.index')->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Visualizar uma categoria especifica
     */
    public function show($id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return redirect()->route('categorias.index')->with('error', 'Categoria não encontrada!');
        }

        return view('categorias.show', compact('categoria'));
    }

    /**
     * Retornar a view de edição
     */
    public function edit($id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return redirect()->route('categorias.index')->with('error', 'Categoria não encontrada!');
        }

        return view('categorias.edit', compact('categoria'));
    }


    /**
     * Atualiza informacoes da categoria
     */
    public function update(Request $request, $id)
    {

        $categoria = Categoria::find($id);

        if (!$categoria) {
            return redirect()->route('categorias.index')->with('error', 'Categoria não encontrada!');
        }

        $validator = Validator::make(
            $request->all(),
            [
                'nome_categoria' => 'required|string|min:2|max:30',
                'descricao_categoria' => 'required|string|min:2|max:255',
                'status_categoria' => 'required|in:Ativo,Inativo',
            ],
            [
                'required' => 'O campo :attribute é obrigatório.',
                'exists' => 'O campo :attribute nao existe.',
                'string' => 'O campo :attribute deve ser uma string.',
                'in' => 'O campo :attribute deve ser "Ativo" ou "Inativo".',
                'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
                'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            ],
            [
                'nome_categoria' => 'Nome da categoria',
                'descricao_categoria' => 'Descrição da categoria',
                'status_categoria' => 'Status da categoria',
            ]
        );

        if ($validator->fails()) {
            // Se a validação falhar, redireciona com erros
            return redirect()->route('categorias.edit', $categoria->id)
                ->withErrors($validator)
                ->withInput();
        }

        // Atualiza a categoria com os novos dados
        $categoria->update($request->all());

        // Redireciona para a lista de categorias com sucesso
        return redirect()->route('categorias.index')->with('success', 'Categoria atualizada com sucesso!');
    }
}
