@extends('layouts.index')

<!-- Modal Informativo de Quantidades -->
<div class="modal fade" id="produtoModal" tabindex="-1" aria-labelledby="produtoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="produtoModalLabel">Detalhes do Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Categoria:</strong> <span id="produtoCategoria"></span></p>
                <p><strong>Nome:</strong> <span id="produtoNome"></span></p>
                <p><strong>Quantidade Total:</strong> <span id="produtoQuantidade"></span></p>
                <p><strong>Quantidade Minima:</strong> <span id="produtoQuantidadeMinima"></span></p>
                <p><strong>Quantidade Maxima:</strong> <span id="produtoQuantidadeMaxima"></span></p>
                <p><strong>Preço:</strong> R$ <span id="produtoPreco"></span></p>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Descarte -->
<div class="modal fade" id="descarteModal" tabindex="-1" aria-labelledby="descarteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="descarteModalLabel">Baixa de Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="descarteForm"
                action="{{ route('estoques.produtos.descarte', ['estoque' => $estoque->id, 'pivotId' => ':pivotId']) }}"
                method="POST" onsubmit="handleDescarteFormSubmit(event)">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quantidade_descarte" class="form-label">Quantidade Baixa*</label>
                        <input type="number" class="form-control" id="quantidade_descarte" name="quantidade_descarte"
                            min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="defeito_descarte" class="form-label">Motivo*</label>
                        <select class="form-control" id="defeito_descarte" name="defeito_descarte">
                            <option value="">Selecione o motivo</option>
                            <option value="Utilizado">Utilizado</option>
                            <option value="Vencimento">Vencimento</option>
                            <option value="Improprio para Consumo">Impróprio para consumo</option>
                            <option value="Danificado">Danificado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-danger">Dar Baixa</button>
                </div>
            </form>

        </div>
    </div>
</div>


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
    <h3>Produtos no Estoque: {{ $estoque->nome_estoque }}</h3>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            <a href="{{ route('estoques.index', ['escola' => $estoque->local->id]) }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Voltar</a>
            <a href="{{ route('estoques.produtos.create', $estoque->id) }}" class="btn btn-success"><i
                    class="fa-solid fa-plus"></i>
                Novo</a>
            <a href="{{ route('estoques.baixas.show', $estoque->id) }}" class="btn btn-danger">
                <i class="fa-solid fa-recycle"></i></i>
                Baixas</a>

            <a href="#" class="btn btn-info" id="openCalendar"><i class="fa-solid fa-filter"></i> Filtrar</a>

        </div>


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


        <a href="{{ route('estoques.baixas.show', $estoque->id) }}" class="btn btn-sm">
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
        </a>

    </div>



    <table class="table table-striped mt-3">

        <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Valor</th>
                <th>Qtd. Atual</th>
                <th>Validade</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($estoque->produtos as $produto)
                <tr class="produto-item" data-produto-id="{{ $produto->id }}" data-nome="{{ $produto->nome_produto }}"
                    data-preco="{{ $produto->preco }}" data-quantidade-minima="{{ $produto->pivot->quantidade_minima }}"
                    data-quantidade-maxima="{{ $produto->pivot->quantidade_maxima }}"
                    data-categoria="{{ $produto->categoria->nome_categoria }}"
                    data-quantidade="{{ $produto->pivot->quantidade_atual }}">
                    <td>{{ $produto->pivot->id }}</td>
                    <td>{{ $produto->nome_produto }}</td>
                    <td>{{ $produto->preco }}</td>

                    <td>{{ $produto->pivot->quantidade_atual }}</td>
                    <td>{{ $produto->pivot->validade ? \Carbon\Carbon::parse($produto->pivot->validade)->format('d-m-Y') : 'Sem validade' }}
                    </td>
                    <td>

                        <!-- Botões para editar e dar baixa no produto -->

                        <a href="{{ route('estoques.produtos.edit', ['estoque' => $estoque->id, 'pivotId' => $produto->pivot->id]) }}"
                            class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i>
                        </a>

                        <a href="#" data-bs-toggle="modal" data-bs-target="#descarteModal"
                            data-quantidade="{{ $produto->pivot->quantidade_atual }}"
                            data-pivotid="{{ $produto->pivot->id }}" class="btn btn-danger btn-sm">
                            <i class="fa-solid fa-recycle"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>


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
                    `{{ route('estoques.show', ['escola' => $escola->id, 'estoque' => $estoque->id]) }}?data_inicio=${startDate}&data_fim=${endDate}`;
            } else {
                alert('Por favor, selecione ambas as datas.');
            }

            // Fecha o modal após aplicar o filtro
            calendarModal.hide();
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('produtoModal'));
        const produtoItems = document.querySelectorAll('.produto-item');

        produtoItems.forEach(item => {
            item.addEventListener('dblclick', function() {
                console.log("Produto clicado:", this);

                const produtoId = this.getAttribute('data-produto-id');
                const nomeProduto = this.getAttribute('data-nome');
                const preco = parseFloat(this.getAttribute('data-preco'));
                const quantidadeMinima = parseInt(this.getAttribute('data-quantidade-minima'));
                const quantidadeMaxima = parseInt(this.getAttribute('data-quantidade-maxima'));
                const categoria = this.getAttribute('data-categoria');
                const quantidadeAtual = parseInt(this.getAttribute('data-quantidade'));

                // Soma a quantidade total do produto
                let quantidadeTotal = 0;
                const produtosDoMesmoProduto = document.querySelectorAll(
                    `[data-produto-id="${produtoId}"]`);
                produtosDoMesmoProduto.forEach(produto => {
                    quantidadeTotal += parseInt(produto.getAttribute(
                        'data-quantidade'));
                });

                // Preenche o modal com as informações
                document.getElementById('produtoCategoria').textContent = categoria;
                document.getElementById('produtoNome').textContent = nomeProduto;
                document.getElementById('produtoQuantidade').textContent = quantidadeTotal;
                document.getElementById('produtoQuantidadeMinima').textContent =
                    quantidadeMinima;
                document.getElementById('produtoQuantidadeMaxima').textContent =
                    quantidadeMaxima;
                document.getElementById('produtoPreco').textContent = preco.toFixed(
                    2); // Formatação para preço

                // Exibe o modal
                console.log("Abrindo o modal...");
                modal.show();
            });
        });
    });
</script>

<script>
    let quantidadeMaxima; // Variável global para guardar a quantidade máxima
    let pivotId; // Variável global para guardar o pivot ID

    // Evento Global de Clique para capturar o clique do botão
    document.addEventListener('click', (event) => {
        const btnDescarte = event.target.closest('[data-bs-target="#descarteModal"]');
        if (btnDescarte) {
            // Obtendo os valores dos atributos data-* do botão
            quantidadeMaxima = parseInt(btnDescarte.dataset.quantidade, 10);
            pivotId = btnDescarte.dataset.pivotid;

            console.log('Quantidade Máxima do Produto:', quantidadeMaxima);
            console.log('Pivot ID do Produto:', pivotId);
        }
    });

    function handleDescarteFormSubmit(event) {
        event.preventDefault(); // Impede o envio padrão do formulário

        const form = event.target;
        if (!form) {
            console.error("Formulário não encontrado.");
            return;
        }

        const quantidadeDescarte = parseInt(form.querySelector('#quantidade_descarte').value, 10);
        const url = form.getAttribute('action'); // Certifique-se de que o form tem o atributo 'action'

        // Substituir o valor do :pivotId na URL
        const urlComPivotId = url.replace(':pivotId', pivotId);

        // Verificar se o URL foi encontrado
        if (!urlComPivotId) {
            console.error("A URL de envio do formulário não foi definida.");
            return;
        }

        console.log('Quantidade Descarte = ' + quantidadeDescarte);
        console.log('Quantidade Máxima = ' + quantidadeMaxima);
        console.log('Pivot ID = ' + pivotId);

        // Validação da quantidade de descarte
        if (isNaN(quantidadeDescarte) || quantidadeDescarte < 1 || quantidadeDescarte > quantidadeMaxima) {
            form.querySelector('#quantidade_descarte').classList.add('is-invalid');
            alert(
                "A quantidade de descarte precisa ser um número válido e não pode ser maior que a quantidade disponível."
            );
            return;
        }

        form.querySelector('#quantidade_descarte').classList.remove('is-invalid');

        // Envio via AJAX
        const formData = new FormData(form);

        fetch(urlComPivotId, {
                method: 'POST',
                body: formData,
            })
            .then(response => {
                // Verifique se a resposta é uma página HTML ou erro
                if (response.headers.get('Content-Type').includes('text/html')) {
                    return response.text().then(text => {
                        console.error("Erro HTML recebido:", text);
                        throw new Error('A resposta não é JSON.');
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log("Descarte realizado com sucesso:", data);

                // Fechar o modal
                const descarteModal = document.getElementById('descarteModal');
                const modal = bootstrap.Modal.getInstance(descarteModal);
                modal.hide(); // Fecha o modal

                // Atualiza a página (recarrega)
                window.location.reload();
            })
            .catch(error => {
                console.error("Erro ao realizar descarte:", error);
            });
    }
</script>
