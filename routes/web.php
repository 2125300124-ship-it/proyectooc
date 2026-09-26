<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministradorController;

Route::get('/', function () {
    return view('coleccionables.index');
})->name('inicio');

Route::get('/inicio', function () {
    return view('coleccionables.index');
})->name('inicio');

Route::get('/clientes', function () {
    return view('clientes.listado', ['clientes' => []]);
})->name('clientes.listado');

Route::get('/clientes/crear', function () {
    return view('clientes.registro');
})->name('clientes.registro');

Route::get('/administradores', [AdministradorController::class, 'listar'])->name('administradores.listado');

Route::get('/administradores/crear', function () {
    return view('administradores.registro');
})->name('administradores.registro');

Route::get('/admin/listar', [AdministradorController::class, 'listar'])->name('admin.index');
Route::get('/admin/crear', [AdministradorController::class, 'vistaFormulario']);
Route::post('/admin/guardar', [AdministradorController::class, 'registrar']);
Route::get('/admin/editar', [AdministradorController::class, 'vistaEdicion']);
Route::put('/admin/actualizar', [AdministradorController::class, 'actualizar']);
Route::get('/admin/mostrar', [AdministradorController::class, 'vistaMostrar']);
Route::delete('/admin/borrar', [AdministradorController::class, 'borrar']);
