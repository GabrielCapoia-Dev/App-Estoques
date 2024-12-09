@extends('layouts.index')

<!-- Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="filterModalLabel"><i class="fa-solid fa-filter"></i> Filtrar por:</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('estoques.baixas.filtrar', $estoque->id) }}" method="GET">
                    <!-- Motivo -->
                    <div class="mb-4">
                        <label for="defeito_descarte" class="form-label">Motivo <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="defeito_descarte" name="defeito_descarte">
                            <option value="">Filtre por motivo</option>
                            <option value="Utilizado">Utilizado</option>
                            <option value="Vencimento">Vencimento</option>
                            <option value="Improprio para Consumo">Impróprio para consumo</option>
                            <option value="Danificado">Danificado</option>
                        </select>
                    </div>

                    <!-- Período -->
                    <p class="fw-bold">Período</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="validade-Inicio" class="form-label">Início</label>
                                <input type="date" class="form-control" id="validade-Inicio" name="validade-Inicio">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="validade-Fim" class="form-label">Fim</label>
                                <input type="date" class="form-control" id="validade-Fim" name="validade-Fim">
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-check"></i> Aplicar Filtros
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@section('content')
    <a href="{{ route('estoques.show', ['escola' => $escola, 'estoque' => $estoque->id]) }}" class="btn btn-sm">
        <h1 class="h3">Baixas do estoque: {{ $estoque->nome_estoque }}</h1>
    </a>



    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            <a href="{{ route('estoques.index', ['escola' => $estoque->local->id]) }}" class="btn btn-secondary mt-3">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>

            <a href="#" class="btn btn-info mt-3" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fa-solid fa-filter"></i> Filtros
            </a>

            <a href="{{ route('estoques.show', ['escola' => $escola, 'estoque' => $estoque->id]) }}"
                class="btn btn-success mt-3"><i class="fa-solid fa-boxes-stacked"></i> Estoque
            </a>


        </div>

        <!-- Card para valor total -->
        <div class="card p-2"
            style="min-width: 300px; display: flex; align-items: center; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div class="card-body p-0 d-flex justify-content-between w-100">
                <div class="text-muted" style="font-size: 0.8rem;">
                    Total Baixa:
                </div>
                <div class="text-danger" style="font-size: 1rem; font-weight: bold;">
                    -R$ {{ $totalBaixas ?? '0,00' }}
                </div>
            </div>
        </div>
    </div>



    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>($)Produto</th>
                <th>Qntd. Baixa</th>
                <th>Motivo</th>
                <th>Validade</th>
                <th>Data do Descarte</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dadosCompletos as $dado)
                <tr>
                    <td>{{ $dado['baixas']['id'] }}</td>
                    <td>{{ $dado['baixas']['nome_produto'] }}</td>
                    <td>{{ $dado['produtos']['preco_produto'] }}</td>
                    <td>{{ $dado['baixas']['quantidade_descarte'] }}</td>
                    <td>{{ $dado['baixas']['defeito_descarte'] }}</td>
                    <td>{{ $dado['baixas']['validade'] }}</td>
                    <td>{{ $dado['baixas']['created_at'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
