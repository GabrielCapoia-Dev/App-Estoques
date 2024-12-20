@extends('layouts.index')

@section('content')
    <div class="container">
        <h1 class="h3">Cadastro de Pedido</h1>


        <!-- Seção de seleção de filtros -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Filtrar</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pedidos.filtrar') }}" id="relatorioForm">
                    <div class="row">

                        <!-- Filtro por local -->
                        <div class="col-md-6">
                            <label for="local" class="form-label">Local</label>
                            <select id="local" name="local" class="form-select">
                                <option value="">Selecione</option>
                                @foreach ($locals as $local)
                                    <option value="{{ $local->id }}">{{ $local->nome_local }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="estoque" class="form-label">Estoque</label>
                            <select id="estoque" name="estoque" class="form-select">
                                <option value="">Selecione</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100" id="filtrarBtn">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Seção de filtros -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5>Lista dos itens filtrados</h5>
                    </div>
                    <div class="card-body">

                        <p>Por favor, aplique os filtros para visualizar os resultados.</p>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Evento de mudança para o select de local
        document.getElementById('local').addEventListener('change', function() {
            const localId = this.value;
            const estoqueSelect = document.getElementById('estoque');

            // Limpa os estoques existentes
            estoqueSelect.innerHTML = '<option value="">Selecione</option>';

            if (localId) {

                const url = `/escolas/${localId}/estoques/getEstoques`;

                // Faz a requisição para a rota que retorna os estoques
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Adiciona os novos <option> para os estoques
                            data.estoques.forEach(estoque => {
                                const option = document.createElement('option');
                                option.value = estoque.id;
                                option.textContent = estoque.nome_estoque;
                                estoqueSelect.appendChild(option);
                            });
                        } else {
                            console.error('Erro ao carregar estoques:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao carregar estoques:', error);
                    });
            }
        });
    });
</script>
