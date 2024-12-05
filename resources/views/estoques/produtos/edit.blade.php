@extends('layouts.index')

@section('content')
    <h1 class="h2">Editar Produto no Estoque</h1>

    <form action="{{ route('estoques.produtos.update', ['estoque' => $estoque->id, 'pivotId' => $pivot->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="quantidade_atual" class="form-label">Quantidade Atual</label>
            <input type="number" name="quantidade_atual" id="quantidade_atual" class="form-control" 
                   value="{{ old('quantidade_atual', $pivot->quantidade_atual) }}" required>
            @error('quantidade_atual')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="quantidade_minima" class="form-label">Quantidade Mínima</label>
            <input type="number" name="quantidade_minima" id="quantidade_minima" class="form-control" 
                   value="{{ old('quantidade_minima', $pivot->quantidade_minima) }}" required>
            @error('quantidade_minima')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="quantidade_maxima" class="form-label">Quantidade Máxima</label>
            <input type="number" name="quantidade_maxima" id="quantidade_maxima" class="form-control" 
                   value="{{ old('quantidade_maxima', $pivot->quantidade_maxima) }}" required>
            @error('quantidade_maxima')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="validade" class="form-label">Validade</label>
            <input type="date" name="validade" id="validade" class="form-control" 
                   value="{{ old('validade', $pivot->validade) }}">
            @error('validade')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <br>
        <button type="submit" class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i> Editar</button>
        <a href="{{ route('estoques.show', ['escola' => $estoque->local->id, 'estoque' => $estoque->id]) }}" 
           class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
    </form>
@endsection
