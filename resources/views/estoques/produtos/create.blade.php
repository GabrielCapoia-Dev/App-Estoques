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
            <input type="number" name="quantidade_atual" id="quantidade_atual" class="form-control" value="{{ old('quantidade_atual') }}" required>
            @error('quantidade_atual')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="quantidade_minima" class="form-label">Quantidade Mínima</label>
            <input type="number" name="quantidade_minima" id="quantidade_minima" class="form-control" value="{{ old('quantidade_minima') }}" required>
            @error('quantidade_minima')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="quantidade_maxima" class="form-label">Quantidade Máxima</label>
            <input type="number" name="quantidade_maxima" id="quantidade_maxima" class="form-control" value="{{ old('quantidade_maxima') }}" required>
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
        <a href="{{ route('estoques.show', ['escola' => $estoque->local->id, 'estoque' => $estoque->id]) }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
    </form>
@endsection
