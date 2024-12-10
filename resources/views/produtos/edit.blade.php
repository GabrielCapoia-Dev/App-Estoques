@extends('layouts.index')

@section('content')
    <h1>Editar Produto</h1>
    <form action="{{ route('produtos.update', $produto->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nome_produto">Nome do Produto</label>
            <input type="text" name="nome_produto" class="form-control"
                value="{{ old('nome_produto', $produto->nome_produto) }}" required>
        </div>

        <div class="form-group">
            <label for="id_categoria">Categoria</label>
            <select name="id_categoria" class="form-control" required>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ $produto->id_categoria == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nome_categoria }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="descricao_produto">Descrição</label>
            <textarea name="descricao_produto" class="form-control" required>{{ old('descricao_produto', $produto->descricao_produto) }}</textarea>
        </div>

        <div class="form-group">
            <label for="preco">Preço</label>
            <input type="text" name="preco" class="form-control" id="preco"
                value="{{ old('preco', $produto->preco) }}" required>
        </div>

        <div class="mb-3">
            <label for="status_produto" class="form-label">Status</label>
            <select class="form-control" id="status_produto" name="status_produto" required>
                <option value="Ativo" {{ $produto->status_produto == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="Inativo" {{ $produto->status_produto == 'Inativo' ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>

        <br>
        <button type="submit" class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i> Editar</button>
        <a href="{{ route('produtos.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>

    </form>
@endsection

<script>
    document.getElementById("preco").addEventListener("input", function(e) {
        let value = e.target.value;

        // Substituir vírgula por ponto
        value = value.replace(",", ".");

        // Limitar o número de casas decimais
        if (value.indexOf('.') !== -1) {
            value = value.replace(/(\.\d{2})\d+$/, '$1'); // Limita a 2 casas decimais
        }

        // Atualiza o valor do campo com a formatação correta
        e.target.value = value;
    });
</script>
