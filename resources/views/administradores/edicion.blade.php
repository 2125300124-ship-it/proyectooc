@extends('layouts.app')

@section('contenido')
<div class="max-w-3xl mx-auto">

    <!-- Encabezado -->
    <div class="mb-6">
        <p class="text-amber-600 font-bold text-xs uppercase tracking-widest mb-1">Módulo administradores</p>
        <h1 class="text-2xl font-bold text-slate-900">Registrar nuevo administrador</h1>
        <p class="text-sm text-slate-500 mt-1">Completa los datos del personal administrativo.</p>
    </div>

    <!-- Alerta de Éxito -->
    @if (session('exito'))
        <div id="alerta-exito" class="flex items-center p-4 mb-6 text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">
            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
            </svg>
            <div class="ms-3 text-sm font-medium">
                {{ session('exito') }}
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alerta-exito" aria-label="Cerrar">
                <span class="sr-only">Cerrar</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Formulario de Flowbite -->
    <div class="p-6 bg-white border border-amber-100 rounded-2xl shadow-xl shadow-amber-100/50 ring-1 ring-slate-900/5">
        <form action="/admin/actualizar" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nombres y Apellidos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nombres" class="block mb-2 text-sm font-medium text-slate-900">Nombres *</label>
                    <input type="text" name="nombres" id="nombres" value="{{ old('nombres') }}" required minlength="2" maxlength="100" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü ]+" title="Solo se permiten letras y espacios" data-letters-only autocomplete="given-name"
                        class="bg-slate-50 border @error('nombres') border-red-500 text-red-900 focus:ring-red-500 focus:border-red-500 @else border-slate-300 text-slate-900 focus:ring-amber-500 focus:border-amber-500 @enderror text-sm rounded-lg block w-full p-2.5"
                        placeholder="Ej. Carlos Alberto">
                    @error('nombres')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="apellidos" class="block mb-2 text-sm font-medium text-slate-900">Apellidos *</label>
                    <input type="text" name="apellidos" id="apellidos" value="{{ old('apellidos') }}" required minlength="2" maxlength="100" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü ]+" title="Solo se permiten letras y espacios" data-letters-only autocomplete="family-name"
                        class="bg-slate-50 border @error('apellidos') border-red-500 text-red-900 focus:ring-red-500 focus:border-red-500 @else border-slate-300 text-slate-900 focus:ring-amber-500 focus:border-amber-500 @enderror text-sm rounded-lg block w-full p-2.5"
                        placeholder="Ej. Gómez Pérez">
                    @error('apellidos')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Correo Electrónico y Nombre de Usuario -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="correo" class="block mb-2 text-sm font-medium text-slate-900">Correo electrónico *</label>
                    <input type="email" name="correo" id="correo" value="{{ old('correo') }}" required maxlength="150" autocomplete="email"
                        class="bg-slate-50 border @error('correo') border-red-500 text-red-900 focus:ring-red-500 focus:border-red-500 @else border-slate-300 text-slate-900 focus:ring-amber-500 focus:border-amber-500 @enderror text-sm rounded-lg block w-full p-2.5"
                        placeholder="ejemplo@dominio.com">
                    @error('correo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="usuario" class="block mb-2 text-sm font-medium text-slate-900">Usuario *</label>
                    <input type="text" name="usuario" id="usuario" value="{{ old('usuario') }}" required minlength="3" maxlength="50" pattern="[A-Za-z0-9_.-]+" autocomplete="username"
                        class="bg-slate-50 border @error('usuario') border-red-500 text-red-900 focus:ring-red-500 focus:border-red-500 @else border-slate-300 text-slate-900 focus:ring-amber-500 focus:border-amber-500 @enderror text-sm rounded-lg block w-full p-2.5"
                        placeholder="carlos_gomez">
                    @error('usuario')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contraseña y Rol -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contrasena" class="block mb-2 text-sm font-medium text-slate-900">Contraseña *</label>
                    <input type="password" name="contrasena" id="contrasena" required minlength="8" maxlength="100" autocomplete="new-password"
                        class="bg-slate-50 border @error('contrasena') border-red-500 text-red-900 focus:ring-red-500 focus:border-red-500 @else border-slate-300 text-slate-900 focus:ring-amber-500 focus:border-amber-500 @enderror text-sm rounded-lg block w-full p-2.5"
                        placeholder="••••••••">
                    @error('contrasena')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="rol" class="block mb-2 text-sm font-medium text-slate-900">Rol *</label>
                    <select name="rol" id="rol" required
                        class="bg-slate-50 border @error('rol') border-red-500 text-red-900 focus:ring-red-500 focus:border-red-500 @else border-slate-300 text-slate-900 focus:ring-amber-500 focus:border-amber-500 @enderror text-sm rounded-lg block w-full p-2.5">
                        <option value="" disabled selected>Selecciona un rol</option>
                        <option value="Dueño" {{ old('rol') == 'Dueño' ? 'selected' : '' }}>Dueño</option>
                        <option value="Inventario" {{ old('rol') == 'Inventario' ? 'selected' : '' }}>Inventario</option>
                        <option value="Ventas" {{ old('rol') == 'Ventas' ? 'selected' : '' }}>Ventas</option>
                    </select>
                    @error('rol')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Carga de Imagen de Perfil -->
            <div>
                <label class="block mb-2 text-sm font-medium text-slate-900" for="imagen">Foto de perfil (Opcional)</label>
                <input name="imagen" id="imagen" type="file" accept="image/png,image/jpeg,image/webp" data-max-size="2097152"
                    class="block w-full text-sm text-slate-900 border @error('imagen') border-red-500 @else border-slate-300 @enderror rounded-lg cursor-pointer bg-slate-50 focus:outline-none">
                <p class="mt-1 text-xs text-slate-500">PNG, JPG o WEBP (Máx. 2MB).</p>
                @error('imagen')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Interruptor de Estado de la cuenta -->
            <div class="pt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="hidden" name="estado" value="0">
                    <input type="checkbox" name="estado" value="1" class="sr-only peer" {{ old('estado', '1') == '1' ? 'checked' : '' }}>
                    <div class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-200 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                    <span class="ms-3 text-sm font-medium text-slate-700">Estado activo</span>
                </label>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
                <button type="reset" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-100 focus:ring-4 focus:ring-slate-100">
                    Limpiar
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 focus:ring-4 focus:ring-amber-200">
                    Mostrar Administrador
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
