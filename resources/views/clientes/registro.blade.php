@extends('layouts.app')

@section('contenido')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <p class="text-amber-600 font-bold text-xs uppercase tracking-widest mb-1">Módulo clientes</p>
        <h2 class="text-2xl font-bold text-slate-900">Registrar coleccionista</h2>
        <p class="text-sm text-slate-500 mt-1">Completa los datos del nuevo cliente.</p>
    </div>

    @if ($errors->any())
        <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
            <span class="font-medium">Revisa los campos marcados:</span>
            <ul class="mt-1 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="#" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nombres" class="block mb-2 text-sm font-medium text-slate-900">Nombres *</label>
                <input type="text" name="nombres" id="nombres" value="{{ old('nombres') }}" required minlength="2" maxlength="100" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü ]+" title="Solo se permiten letras y espacios" data-letters-only autocomplete="given-name" class="bg-slate-50 border @error('nombres') border-red-500 @else border-slate-300 @enderror text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="Ej. Laura María">
                @error('nombres')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="apellidos" class="block mb-2 text-sm font-medium text-slate-900">Apellidos *</label>
                <input type="text" name="apellidos" id="apellidos" value="{{ old('apellidos') }}" required minlength="2" maxlength="100" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü ]+" title="Solo se permiten letras y espacios" data-letters-only autocomplete="family-name" class="bg-slate-50 border @error('apellidos') border-red-500 @else border-slate-300 @enderror text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="Ej. Gómez Pérez">
                @error('apellidos')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="correo" class="block mb-2 text-sm font-medium text-slate-900">Correo electrónico *</label>
            <input type="email" name="correo" id="correo" value="{{ old('correo') }}" required maxlength="150" autocomplete="email" class="bg-slate-50 border @error('correo') border-red-500 @else border-slate-300 @enderror text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="coleccionista@dominio.com">
            @error('correo')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="contrasena" class="block mb-2 text-sm font-medium text-slate-900">Contraseña *</label>
            <input type="password" name="contrasena" id="contrasena" required minlength="8" maxlength="100" autocomplete="new-password" class="bg-slate-50 border @error('contrasena') border-red-500 @else border-slate-300 @enderror text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="Mínimo 8 caracteres">
            @error('contrasena')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="direccion" class="block mb-2 text-sm font-medium text-slate-900">Dirección</label>
            <textarea name="direccion" id="direccion" rows="3" maxlength="500" class="bg-slate-50 border @error('direccion') border-red-500 @else border-slate-300 @enderror text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="Dirección de entrega">{{ old('direccion') }}</textarea>
            @error('direccion')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="imagen" class="block mb-2 text-sm font-medium text-slate-900">Imagen de perfil</label>
            <input type="file" name="imagen" id="imagen" accept="image/png,image/jpeg,image/webp" data-max-size="2097152" class="block w-full text-sm text-slate-900 border @error('imagen') border-red-500 @else border-slate-300 @enderror rounded-lg cursor-pointer bg-slate-50 focus:outline-none">
            <p class="mt-1 text-xs text-slate-500">PNG, JPG o WEBP. Tamaño máximo: 2 MB.</p>
            @error('imagen')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-200">
            <label class="inline-flex items-center cursor-pointer">
                <input type="hidden" name="estado" value="0">
                <input type="checkbox" name="estado" value="1" class="sr-only peer" checked>
                <span class="relative w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-amber-200 peer-checked:bg-amber-500 after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></span>
                <span class="ms-3 text-sm font-medium text-slate-700">Cliente activo</span>
            </label>
            <div class="flex gap-3">
                <button type="reset" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-100">Limpiar</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 focus:ring-4 focus:ring-amber-200">Guardar cliente</button>
            </div>
        </div>
    </form>
</div>
@endsection
