@extends('layouts.index')

<!-- Modal para selecionar as datas -->
<div class="modal" tabindex="-1" id="calendarModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Selecione o Período</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="startDate">Data de Início:</label>
                <input type="date" id="startDate" class="form-control" />

                <label for="endDate" class="mt-3">Data de Fim:</label>
                <input type="date" id="endDate" class="form-control" />

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="applyFilter">Aplicar Filtro</button>
            </div>
        </div>
    </div>
</div>


@section('content')
    <h1 class="h2">Estoques: {{ $escola->nome_local }}</h1>
    <h1 class="h4">Status: {{ $estoques->first()->status_estoque ?? 'Inativo' }}</h1>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            <a href="{{ route('escolas.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i>
                Voltar</a>
            <a href="{{ route('estoques.create', $escola->id) }}" class="btn btn-success"><i class="fa-solid fa-plus"></i>
                Novo</a>
            <a href="{{ route('baixas.index') }}" class="btn btn-danger">
                <i class="fa-solid fa-recycle"></i></i>
                Baixas</a>

            <a href="#" class="btn btn-info" id="openCalendar"><i class="fa-solid fa-filter"></i>   Filtrar</a>

            @if (count($estoques) > 0 && $estoques->first()->status_estoque === 'Ativo')
                <a href="{{ route('estoques.index', ['escola' => $escola->id, 'status_estoque' => 'Inativo']) }}"
                    class="btn btn-secondary"  style="padding: 0.6rem"><i class="fa-regular fa-eye-slash"></i>
                </a>
            @endif

            @if (count($estoques) == 0 || (count($estoques) > 0 && $estoques->first()->status_estoque === 'Inativo'))
                <a href="{{ route('estoques.index', ['escola' => $escola->id, 'status_estoque' => 'Ativo']) }}"
                    class="btn btn-secondary"  style="padding: 0.6rem"><i class="fa-regular fa-eye"></i>
                </a>
            @endif
        </div>


        <br>


        <!-- Card para valor total -->
        <div class="card p-2"
            style="min-width: 250px; display: flex; align-items: center; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div class="card-body p-0 d-flex justify-content-between w-100">
                <div class="text-muted" style="font-size: 0.8rem;">
                    Total Estoque:
                </div>
                <div class="text-success" style="font-size: 1rem; font-weight: bold;">
                    R$ {{ $totalEstoque ?? '0,00' }}
                </div>
            </div>
        </div>

        <div class="card p-2"
            style="min-width: 250px; display: flex; align-items: center; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div class="card-body p-0 d-flex justify-content-between w-100">
                <div class="text-muted" style="font-size: 0.8rem;">
                    Total Baixa:
                </div>
                <div class="text-danger" style="font-size: 1rem; font-weight: bold;">
                    -R$ {{ $totalBaixa ?? '0,00' }}
                </div>
            </div>
        </div>


    </div>


    <!-- Tabela para exibir os estoques -->
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
            @foreach ($estoques as $estoque)
                <tr>
                    <td>{{ $estoque->id }}</td>
                    <td>{{ $estoque->nome_estoque }}</td>
                    <td>{{ $estoque->status_estoque }}</td>
                    <td>{{ $estoque->descricao_estoque }}</td>
                    <td>

                        @if ($estoque->status_estoque === 'Ativo')
                            <a href="{{ route('estoques.show', ['escola' => $escola->id, 'estoque' => $estoque->id]) }}"
                                class="btn btn-info btn-sm"><i class="fa-regular fa-eye"></i></a>
                        @endif
                        <a href="{{ route('estoques.edit', ['escola' => $escola->id, 'estoque' => $estoque->id]) }}"
                            class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></i></a>

                        @if ($estoque->status_estoque === 'Ativo')
                            <a href="{{ route('estoques.baixas.show', $estoque->id) }}" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-recycle"></i></i></a>
                        @endif

                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const openCalendarButton = document.getElementById('openCalendar');
        const calendarModal = new bootstrap.Modal(document.getElementById('calendarModal'));
        const applyFilterButton = document.getElementById('applyFilter');

        // Abre o modal quando o botão de filtro for clicado
        openCalendarButton.addEventListener('click', function(e) {
            e.preventDefault();
            calendarModal.show(); // Exibe o modal
        });

        // Ação ao aplicar o filtro
        applyFilterButton.addEventListener('click', function() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            if (startDate && endDate) {
                // Envia os parâmetros de data na URL
                window.location.href =
                    `{{ route('estoques.index', ['escola' => $escola->id]) }}?data_inicio=${startDate}&data_fim=${endDate}`;
            } else {
                alert('Por favor, selecione ambas as datas.');
            }

            // Fecha o modal após aplicar o filtro
            calendarModal.hide();
        });
    });
</script>
