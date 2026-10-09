@extends('layouts.app')

@section('contenido')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium uppercase tracking-wide text-amber-700">Panel administrativo</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                Bienvenido, {{ Auth::guard('admin')->user()->nombres }} {{ Auth::guard('admin')->user()->apellidos }}
            </h1>
            <p class="mt-2 text-slate-600">Has iniciado sesión como {{ Auth::guard('admin')->user()->usuario }}.</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50">
                Cerrar sesión
            </button>
        </form>
    </div>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('productos.listar') }}" class="rounded-xl border border-slate-200 p-5 hover:border-amber-400">
            <h2 class="font-semibold text-slate-900">Productos</h2>
            <p class="mt-1 text-sm text-slate-600">Administrar catálogo e imágenes.</p>
        </a>
        <a href="{{ route('categorias.listar') }}" class="rounded-xl border border-slate-200 p-5 hover:border-amber-400">
            <h2 class="font-semibold text-slate-900">Categorías</h2>
            <p class="mt-1 text-sm text-slate-600">Administrar categorías.</p>
        </a>
        <a href="{{ route('administradores.listar') }}" class="rounded-xl border border-slate-200 p-5 hover:border-amber-400">
            <h2 class="font-semibold text-slate-900">Administradores</h2>
            <p class="mt-1 text-sm text-slate-600">Administrar cuentas del sistema.</p>
        </a>
    </div>
@endsection
