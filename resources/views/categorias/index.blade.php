@extends('layouts.index')

@section('content')
    <style>
        .bg-td:hover {
            background-color: #d0d0d0;
            /* Cor de fundo cinza ao passar o mouse */
        }
    </style>
    <h1 class="h2">Categorias: {{ $categorias->first()->status_categoria ?? 'Inativo' }}</h1>

    <div class="mb-3">
        <a href="{{ route('categorias.create') }}" class="btn btn-success"><i class="fa-solid fa-plus"></i> Novo</a>


        @if (count($categorias) > 0 && $categorias->first()->status_categoria === 'Ativo')
            <a href="{{ route('categorias.index', ['mostrar_inativos' => 'true']) }}" class="btn btn-secondary"><i
                    class="fa-regular fa-eye-slash"></i></a>
        @endif

        @if (count($categorias) == 0 || (count($categorias) > 0 && $categorias->first()->status_categoria === 'Inativo'))
            <a href="{{ route('categorias.index') }}" class="btn btn-secondary"><i class="fa-regular fa-eye"></i></a>
        @endif
    </div>

    <!-- Tabela para exibir as categorias -->
    <table class="table table-striped mt-3">

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
            @foreach ($categorias as $categoria)
                <tr>
                    <td>{{ $categoria->id }}</td>

                    <td class="bg-td" style="transition: background-color 0.3s;">
                        <a href="{{ route('categorias.show', $categoria->id) }}"
                            style="display: block; text-decoration: none; color: black;">
                            {{ $categoria->nome_categoria }}
                        </a>
                    </td>
                    <td>{{ $categoria->status_categoria }}</td>
                    <td>{{ $categoria->descricao_categoria }}</td>
                    <td>
                        <a href="{{ route('categorias.show', $categoria->id) }}" class="btn btn-info btn-sm"><i
                                class="fa-regular fa-eye"></i></a>
                        <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-warning btn-sm"><i
                                class="fa-solid fa-pen-to-square"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
