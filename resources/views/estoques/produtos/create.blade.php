@extends('layouts.index')

@section('content')
    <h1 class="h2">Adicionar Produto ao Estoque</h1>

    <form action="{{ route('estoques.produtos.store', $estoque->id) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="produto_id" class="form-label">Produto</label>
            <select name="produto_id" id="produto_id" class="form-control" required>
                <option value="">Selecione um produto</option>
                @foreach ($produtos as $produto)
                    <option value="{{ $produto->id }}">{{ $produto->nome_produto }}</option>
                @endforeach
            </select>
            @error('produto_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="quantidade_atual" class="form-label">Quantidade Atual</label>
            <input type="number" name="quantidade_atual" id="quantidade_atual" class="form-control"
                value="{{ old('quantidade_atual') }}" required>
            @error('quantidade_atual')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="quantidade_minima" class="form-label">Quantidade Mínima</label>
            <input type="number" name="quantidade_minima" id="quantidade_minima" class="form-control"
                value="{{ old('quantidade_minima') }}" required readonly>
            @error('quantidade_minima')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="quantidade_maxima" class="form-label">Quantidade Máxima</label>
            <input type="number" name="quantidade_maxima" id="quantidade_maxima" class="form-control"
                value="{{ old('quantidade_maxima') }}" required readonly>
            @error('quantidade_maxima')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="validade" class="form-label">Validade</label>
            <input type="date" name="validade" id="validade" class="form-control" value="{{ old('validade') }}">
            @error('validade')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <br>

        <button type="submit" class="btn btn-success"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
        <a href="{{ route('estoques.show', ['escola' => $estoque->local->id, 'estoque' => $estoque->id]) }}"
            class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const produtoSelect = document.getElementById('produto_id');
            const quantidadeMinima = document.getElementById('quantidade_minima');
            const quantidadeMaxima = document.getElementById('quantidade_maxima');

            // Limpar os campos de quantidade mínima e máxima quando o formulário carregar
            quantidadeMinima.value = '';
            quantidadeMaxima.value = '';

            // Tornar os campos apenas leitura (readonly)
            quantidadeMinima.readOnly = true;
            quantidadeMaxima.readOnly = true;
            quantidadeMinima.style.backgroundColor = '#f0f0f0';
            quantidadeMaxima.style.backgroundColor = '#f0f0f0';

            // Dados de quantidades mínimas e máximas dos produtos no estoque
            const produtosEstoque = @json($produtosEstoque);

            // Preencher campos com dados do banco quando um produto for selecionado
            produtoSelect.addEventListener('change', function() {
                const produtoId = produtoSelect.value;

                if (produtoId && produtosEstoque[produtoId]) {
                    // Se o produto for encontrado no estoque, preenche as quantidades
                    const produtoEstoque = produtosEstoque[produtoId];
                    quantidadeMinima.value = produtoEstoque.quantidade_minima;
                    quantidadeMaxima.value = produtoEstoque.quantidade_maxima;

                    // Tornar os campos apenas leitura (readonly)
                    quantidadeMinima.readOnly = true;
                    quantidadeMaxima.readOnly = true;

                    // Estilizar os campos para indicar que estão desabilitados
                    quantidadeMinima.style.backgroundColor = '#f0f0f0';
                    quantidadeMaxima.style.backgroundColor = '#f0f0f0';
                } else {
                    // Caso não tenha o produto, limpa os campos e torna-os editáveis
                    quantidadeMinima.value = '';
                    quantidadeMaxima.value = '';
                    quantidadeMinima.readOnly = false;
                    quantidadeMaxima.readOnly = false;

                    // Restaurar a cor de fundo para editáveis
                    quantidadeMinima.style.backgroundColor = '';
                    quantidadeMaxima.style.backgroundColor = '';
                }
            });
        });
    </script>
@endsection
