@extends('layouts.app')

@section('contenido')
<div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">
    <div>
        <p class="text-amber-600 font-bold text-xs uppercase tracking-[0.2em] mb-2">Módulo clientes</p>
        <h2 class="text-2xl font-bold text-slate-900">Coleccionistas registrados</h2>
    </div>
    <a href="{{ url('/clientes/crear') }}" class="inline-flex items-center justify-center rounded-lg bg-amber-500 px-4 py-2 text-sm font-medium text-white hover:bg-amber-600 focus:outline-none focus:ring-4 focus:ring-amber-200">
        Nuevo cliente
    </a>
</div>

<div class="overflow-x-auto border border-slate-200 rounded-xl">
    <table class="w-full text-left text-sm text-slate-600">
        <thead class="bg-slate-100 text-xs uppercase text-slate-700">
            <tr>
                <th class="px-6 py-3">Imagen</th>
                <th class="px-6 py-3">Nombres</th>
                <th class="px-6 py-3">Apellidos</th>
                <th class="px-6 py-3">Correo</th>
                <th class="px-6 py-3">Dirección</th>
                <th class="px-6 py-3">Estado</th>
                <th class="px-6 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($clientes ?? [] as $cliente)
                <tr class="border-t border-slate-200 bg-white hover:bg-slate-50">
                    <td class="px-6 py-4">
                        @if (!empty($cliente->imagen))
                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $cliente->imagen) }}" alt="">
                        @else
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-sm font-bold text-amber-700">{{ strtoupper(substr($cliente->nombres ?? 'C', 0, 1)) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium text-slate-900">{{ $cliente->nombres }}</td>
                    <td class="px-6 py-4">{{ $cliente->apellidos }}</td>
                    <td class="px-6 py-4">{{ $cliente->correo }}</td>
                    <td class="px-6 py-4">{{ $cliente->direccion ?: 'Sin dirección' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $cliente->estado ? 'bg-green-100 text-green-800' : 'bg-slate-200 text-slate-600' }}">
                            {{ $cliente->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="#" class="rounded-lg bg-amber-100 px-3 py-2 text-xs font-medium text-amber-700 hover:bg-amber-200">Editar</a>
                            <button type="button" class="rounded-lg bg-red-100 px-3 py-2 text-xs font-medium text-red-700 hover:bg-red-200">Eliminar</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">No hay clientes para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
