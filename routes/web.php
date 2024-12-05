<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\DescarteProdutoController;
use App\Http\Controllers\EstoqueProdutoController;
use App\Http\Controllers\HistoricoProdutoController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\UsuarioController;
use App\Models\HistoricoProduto;
use Illuminate\Support\Facades\Route;

Route::prefix('usuarios')->name('usuarios.')->group(function () {
    Route::get('/listar', [UsuarioController::class, 'index'])->name('index');
    Route::get('/create', [UsuarioController::class, 'create'])->name('create');
    Route::post('/store', [UsuarioController::class, 'store'])->name('store');
    Route::get('/{id}/visualizar', [UsuarioController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [UsuarioController::class, 'edit'])->name('edit');
    Route::put('/{id}/update', [UsuarioController::class, 'update'])->name('update');
});

Route::prefix('escolas')->name('escolas.')->group(function () {
    Route::get('/listar', [LocalController::class, 'index'])->name('index');
    Route::get('/create', [LocalController::class, 'create'])->name('create');
    Route::post('/store', [LocalController::class, 'store'])->name('store');
    Route::get('/{id}/visualizar', [LocalController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [LocalController::class, 'edit'])->name('edit');
    Route::put('/{id}/update', [LocalController::class, 'update'])->name('update');
    Route::post('{id_local}/vincular', [LocalController::class, 'vincularUsuario'])->name('vincularUsuario');
    Route::delete('{id_local}/desvincular/{usuario_id}', [LocalController::class, 'desvincularUsuario'])->name('desvincularUsuario');
});

Route::prefix('escolas/{escola}/estoques')->name('estoques.')->group(function () {
    Route::get('/listar', [EstoqueController::class, 'index'])->name('index');
    Route::get('/create', [EstoqueController::class, 'create'])->name('create');
    Route::post('/criarEstoque', [EstoqueController::class, 'criarEstoque'])->name('criarEstoque');
    Route::get('/{estoque}/visualizar', [EstoqueController::class, 'show'])->name('show');
    Route::get('/{estoque}/edit', [EstoqueController::class, 'edit'])->name('edit');
    Route::put('/{estoque}/update', [EstoqueController::class, 'update'])->name('update');
});

Route::prefix('categorias')->name('categorias.')->group(function () {
    Route::get('/listar', [CategoriaController::class, 'index'])->name('index');
    Route::get('/create', [CategoriaController::class, 'create'])->name('create');
    Route::post('/store', [CategoriaController::class, 'store'])->name('store');
    Route::get('/{id}/visualizar', [CategoriaController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [CategoriaController::class, 'edit'])->name('edit');
    Route::put('/{id}/update', [CategoriaController::class, 'update'])->name('update');
});

Route::prefix('produtos')->name('produtos.')->group(function () {
    Route::get('/listar', [ProdutoController::class, 'index'])->name('index');
    Route::get('/create', [ProdutoController::class, 'create'])->name('create');
    Route::post('/store', [ProdutoController::class, 'store'])->name('store');
    Route::get('/{produto}/visualizar', [ProdutoController::class, 'show'])->name('show');
    Route::get('/{produto}/edit', [ProdutoController::class, 'edit'])->name('edit');
    Route::put('/{produto}/update', [ProdutoController::class, 'update'])->name('update');
});

Route::prefix('categorias/{categoria}/produtos')->name('categorias.produtos.')->group(function () {
    Route::get('/create', [ProdutoController::class, 'create'])->name('create');
    Route::post('/store', [ProdutoController::class, 'store'])->name('store');
    Route::get('/{produto}/edit', [ProdutoController::class, 'edit'])->name('edit');
    Route::put('/{produto}/update', [ProdutoController::class, 'update'])->name('update');
});

Route::prefix('estoques/{estoque}/produtos')->name('estoques.produtos.')->group(function () {
    Route::get('/create', [EstoqueProdutoController::class, 'create'])->name('create');
    Route::post('/store', [EstoqueProdutoController::class, 'store'])->name('store');
    Route::get('/{pivotId}/edit', [EstoqueProdutoController::class, 'edit'])->name('edit');
    Route::put('/{pivotId}/update', [EstoqueProdutoController::class, 'update'])->name('update');
    Route::post('/{pivotId}/descarte', [DescarteProdutoController::class, 'store'])->name('descarte');
    Route::get('/{produto}/visualizar', [EstoqueProdutoController::class, 'show'])->name('show');
});

Route::prefix('estoques/{estoque}/produtos')->name('historico.produtos.')->group(function () {
    Route::get('/{produto}/historico', [HistoricoProdutoController::class, 'index'])->name('index');
});
