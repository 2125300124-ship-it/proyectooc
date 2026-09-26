@extends('layouts.app')

@section('contenido')
<div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-amber-600 font-bold text-xs uppercase tracking-widest mb-1">Módulo administradores</p>
        <h2 class="text-2xl font-bold text-slate-900">Administradores registrados</h2>
        <p class="text-sm text-slate-500 mt-1">Consulta el personal con acceso a la gestión de la tienda.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ url('/admin/crear') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 focus:ring-4 focus:ring-amber-200">Nuevo administrador</a>
    </div>
</div>

@if (session('exito'))
    <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">{{ session('exito') }}</div>
@endif

<div class="relative overflow-x-auto border border-slate-200 rounded-lg shadow-sm">
    <table class="w-full text-sm text-left text-slate-600">
        <caption class="sr-only">Listado de administradores</caption>
        <thead class="text-xs text-slate-700 uppercase bg-slate-100">
            <tr>
                <th scope="col" class="px-6 py-4">Imagen</th>
                <th scope="col" class="px-6 py-4">Nombres</th>
                <th scope="col" class="px-6 py-4">Apellidos</th>
                <th scope="col" class="px-6 py-4">Correo</th>
                <th scope="col" class="px-6 py-4">Usuario</th>
                <th scope="col" class="px-6 py-4">Rol</th>
                <th scope="col" class="px-6 py-4">Estado</th>
                <th scope="col" class="px-6 py-4"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($admins as $admin)
                <tr class="bg-white border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-6 py-4">
                        @if (!empty($admin->imagen))
                            <img class="w-10 h-10 rounded-full object-cover" src="{{ asset('storage/' . $admin->imagen) }}" alt="{{ $admin->nombres }}">
                        @else
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-amber-100 text-amber-700 font-bold">{{ strtoupper(substr($admin->nombres ?? 'A', 0, 1)) }}</span>
                        @endif
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-slate-900 whitespace-nowrap">{{ $admin->nombres }}</th>
                    <td class="px-6 py-4">{{ $admin->apellidos }}</td>
                    <td class="px-6 py-4">{{ $admin->correo }}</td>
                    <td class="px-6 py-4">{{ $admin->usuario }}</td>
                    <td class="px-6 py-4">{{ $admin->rol }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $admin->estado ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $admin->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ url('/admin/editar') }}" class="inline-flex items-center px-3 py-2 text-xs font-medium text-amber-700 bg-amber-100 rounded-lg hover:bg-amber-200" aria-label="Editar administrador">Editar</a>
                            <form action="{{ url('/admin/borrar') }}" method="POST" onsubmit="return confirm('¿Deseas eliminar este administrador?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-2 text-xs font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200" aria-label="Eliminar administrador">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="bg-white">
                    <td colspan="8" class="px-6 py-12 text-center text-slate-500">No hay administradores para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
