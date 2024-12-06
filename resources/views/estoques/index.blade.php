@extends('layouts.index')

@section('content')
    <h1 class="h2">Estoques da Escola: {{ $escola->nome_local }}</h1>

    <div class="mb-3">
        <a href="{{ route('escolas.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
        <a href="{{ route('estoques.create', $escola->id) }}" class="btn btn-success"><i class="fa-solid fa-plus"></i> Novo</a>
    </div>

    <!-- Tabela para exibir os estoques -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Status</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($estoques as $estoque)
                <tr>
                    <td>{{ $estoque->id }}</td>
                    <td>{{ $estoque->nome_estoque }}</td>
                    <td>{{ $estoque->status_estoque }}</td>
                    <td>{{ $estoque->descricao_estoque }}</td>
                    <td>
                        <a href="{{ route('estoques.show', ['escola' => $escola->id, 'estoque' => $estoque->id]) }}"
                            class="btn btn-info btn-sm"><i class="fa-regular fa-eye"></i></a>
                        <a href="{{ route('estoques.edit', ['escola' => $escola->id, 'estoque' => $estoque->id]) }}"
                            class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></i></a>
                        <a href="{{ route('estoques.baixas.listar', $estoque->id) }}"
                            class="btn btn-danger btn-sm"><i class="fa-solid fa-recycle"></i></i></a>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
