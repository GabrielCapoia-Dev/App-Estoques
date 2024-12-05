@extends('layouts.index')

<!-- Modal -->
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
                <h5 class="modal-title" id="descarteModalLabel">Descarte de Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="descarteForm"
                action="{{ route('estoques.produtos.descarte', ['estoque' => $estoque->id, 'pivotId' => ':pivotId']) }}"
                method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quantidade_descarte" class="form-label">Quantidade a Descartar</label>
                        <input type="number" class="form-control" id="quantidade_descarte" name="quantidade_descarte"
                            min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="defeito_descarte" class="form-label">Defeito (opcional)</label>
                        <textarea class="form-control" id="defeito_descarte" name="defeito_descarte"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="descricao_descarte" class="form-label">Descrição do Descarte</label>
                        <textarea class="form-control" id="descricao_descarte" name="descricao_descarte"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-danger">Descartar</button>
                </div>
            </form>
        </div>
    </div>
</div>


@section('content')
    <h1>Detalhes do Estoque: {{ $estoque->nome_estoque }}</h1>

    <!-- Tabela de produtos no estoque -->
    <h3>Produtos no Estoque</h3>

    <!-- Botão para adicionar um novo produto ao estoque -->
    <a href="{{ route('estoques.produtos.create', $estoque->id) }}" class="btn btn-success mt-3">
        <i class="fa-solid fa-plus"></i> Novo
    </a>

    <!-- Botões de navegação -->
    <a href="{{ route('estoques.index', ['escola' => $estoque->local->id]) }}" class="btn btn-secondary mt-3">
        <i class="fa-solid fa-arrow-left"></i> Voltar
    </a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome do Produto</th>
                <th>Quantidade Atual</th>
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
                    <td>{{ $produto->nome_produto }}</td>
                    <td>{{ $produto->pivot->quantidade_atual }}</td>
                    <td>{{ $produto->pivot->validade }}</td>
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


                        <a href="{{ route('historico.produtos.index', ['estoque' => $estoque->id, 'produto' => $produto->id]) }}"
                            class="btn btn-info btn-sm"><i class="fa-solid fa-clock-rotate-left"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

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
    $(document).ready(function() {
        // Quando o modal for aberto
        $('#descarteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var quantidadeAtual = button.data('quantidade');
            var pivotId = button.data('pivotid');

            var modal = $(this);
            modal.find('#quantidade_descarte').attr('max', quantidadeAtual);
            modal.find('#quantidade_descarte').val('');
            modal.find('#quantidade_descarte').removeClass('is-invalid');
            modal.find('form').attr('action', '/estoques/{{ $estoque->id }}/produtos/descarte/' +
                pivotId);
        });


        // Adicionando um listener para antes do envio do formulário
        $('#descarteModal form').submit(function(event) {
            event.preventDefault(); // Impede o envio padrão do formulário

            var quantidadeDescarte = $('#quantidade_descarte').val();
            var quantidadeMaxima = $('#quantidade_descarte').attr('max');

            // Verifica se a quantidade informada é válida
            if (!quantidadeDescarte || quantidadeDescarte < 1 || quantidadeDescarte >
                quantidadeMaxima) {
                $('#quantidade_descarte').addClass('is-invalid'); // Marca o campo com erro
                alert(
                    "A quantidade de descarte precisa ser um número válido e não pode ser maior que a quantidade disponível."
                );
            } else {
                $('#quantidade_descarte').removeClass(
                    'is-invalid'); // Remove a classe de erro se válido

                // Captura o ID do produto (do botão que abriu o modal)
                var pivotId = button.data(
                    'pivotid'); // Adicione o atributo 'data-pivotid' no botão de descarte

                // Usando AJAX para enviar o formulário de descarte
                $.ajax({
                    url: '/estoques/' + {{ $estoque->id }} + '/produtos/descarte/' +
                        pivotId, // Altere com o endpoint correto
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Adiciona o token CSRF
                        quantidade_descarte: quantidadeDescarte,
                        defeito_descarte: $('#defeito_descarte').val(),
                        descricao_descarte: $('#descricao_descarte').val()
                    },
                    success: function(response) {
                        console.log("Dados alterados com sucesso:", response);
                        // Aqui você pode fazer algo como atualizar a interface, mostrar uma mensagem de sucesso, etc.
                        $('#descarteModal').modal('hide'); // Fecha o modal após sucesso
                    },
                    error: function(xhr, status, error) {
                        console.log("Erro ao tentar alterar os dados:", xhr.responseText);
                        // Aqui você pode lidar com erros, por exemplo, mostrando uma mensagem ao usuário
                    }
                });
            }
        });
    });
</script>
