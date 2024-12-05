@extends('layouts.index')

@section('content')
    <h1>Criar Produto</h1>
    <form action="{{ route('produtos.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nome_produto">Nome do Produto</label>
            <input type="text" name="nome_produto" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="id_categoria">Categoria</label>
            <select name="id_categoria" class="form-control" required>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nome_categoria }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="descricao_produto">Descrição</label>
            <textarea name="descricao_produto" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label for="preco">Preço</label>
            <input type="text" name="preco" class="form-control" id="preco">
        </div>

        <br>
        <button type="submit" class="btn btn-success"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
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
