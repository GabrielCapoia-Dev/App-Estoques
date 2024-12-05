@extends('layouts.index')

@section('content')
    <h1>Lista de Escolas</h1>

    <div class="mb-3">
        <a href="{{ route('escolas.create') }}" class="btn btn-success"><i class="fa-solid fa-plus"></i> Novo</a>
    </div>

    <!-- Tabela para exibir as escolas -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($escolas as $escola)
                <tr>
                    <td>{{ $escola->id }}</td>
                    <td>{{ $escola->nome_local }}</td>
                    <td>{{ $escola->status_local }}</td>
                    <td>
                        <!-- Link para ver mais detalhes da escola -->
                        <a href="{{ route('escolas.show', $escola->id) }}" class="btn btn-info btn-sm"><i class="fa-regular fa-eye"></i></a>
                        
                        <!-- Link para editar a escola -->
                        <a href="{{ route('escolas.edit', $escola->id) }}" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen"></i></a>
                        
                        <!-- Link para listar estoques da escola -->
                        <a href="{{ route('estoques.index', $escola->id) }}" class="btn btn-success btn-sm"><i class="fa-solid fa-boxes-stacked"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
