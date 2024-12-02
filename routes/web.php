<?php

use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
Route::get('/usuarios/{id}', [UsuarioController::class, 'show'])->name('usuarios.show');
Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
Route::put('/usuarios/{id}/desativar', [UsuarioController::class, 'desativarUsuario'])->name('usuarios.desativar');
Route::put('/usuarios/{id}/ativar', [UsuarioController::class, 'ativarUsuario'])->name('usuarios.ativar');
Route::put('/usuarios/{id}/status', [UsuarioController::class, 'atualizarStatus'])->name('usuarios.status');
Route::get('/usuarios/permissao/{permissao}', [UsuarioController::class, 'visualizarUsuariosPorPermissao'])->name('usuarios.permissao');
Route::get('/usuarios/ativos', [UsuarioController::class, 'visualizarUsuariosAtivos'])->name('usuarios.ativos');
Route::get('/usuarios/inativos', [UsuarioController::class, 'visualizarUsuariosInativos'])->name('usuarios.inativos');

Route::get('/escolas', [LocalController::class, 'index'])->name('escolas.index');
Route::get('/escolas/create', [LocalController::class, 'create'])->name('escolas.create');
Route::post('/escolas', [LocalController::class, 'store'])->name('escolas.store');
Route::get('/escolas/{id}', [LocalController::class, 'show'])->name('escolas.show');
Route::get('/escolas/{id}/edit', [LocalController::class, 'edit'])->name('escolas.edit');
Route::put('/escolas/{id}', [LocalController::class, 'update'])->name('escolas.update');
Route::get('/escolas/{id}/funcionarios', [LocalController::class, 'listarFuncionarios'])->name('escolas.listar_funcionarios');
Route::post('escolas/{local_id}/vincular', [LocalController::class, 'vincularUsuario'])->name('escolas.vincularUsuario');
Route::delete('escolas/{local_id}/desvincular/{usuario_id}', [LocalController::class, 'desvincularUsuario'])->name('escolas.desvincularUsuario');
Route::get('escolas/{escola}/estoques', [EstoqueController::class, 'estoquesPorEscola'])->name('escolas.estoques');
