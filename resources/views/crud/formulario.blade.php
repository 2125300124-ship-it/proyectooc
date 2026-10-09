<div class="max-w-3xl">
    <div class="mb-6">
        <p class="text-amber-600 font-bold text-xs uppercase tracking-widest mb-1">{{ $modelo }}</p>
        <h1 class="text-2xl font-bold text-slate-900">{{ $modo === 'crear' ? 'Registrar' : 'Editar' }}: {{ $titulo }}</h1>
        <p class="text-sm text-slate-500 mt-1">Completa los campos requeridos.</p>
    </div>

    @if ($errors->any())
        <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
            <ul class="list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if ($modo === 'crear')
        <form action="{{ route($modulo . '.registrar') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @else
        <form action="{{ route($modulo . '.actualizar', $id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @method('PUT')
    @endif
        @csrf
        @foreach ($campos as $campo)
            @php($nombre = $campo['name'])
            @php($tipo = $campo['type'] ?? 'text')
            @php($valor = old($nombre, $registro[$campo['column'] ?? $nombre] ?? ''))
            <div>
                <label for="{{ $nombre }}" class="block mb-2 text-sm font-medium text-slate-900">{{ $campo['label'] }}{{ ($campo['required'] ?? false) ? ' *' : '' }}</label>
                @if ($tipo === 'select')
                    <select id="{{ $nombre }}" name="{{ $nombre }}" @required($campo['required'] ?? false) class="block w-full p-2.5 text-sm border rounded-lg @error($nombre) border-red-500 @else border-slate-300 @enderror">
                        <option value="">Selecciona una opción</option>
                        @foreach ($campo['options'] as $opcion => $etiqueta)
                            <option value="{{ $opcion }}" @selected((string) $valor === (string) $opcion)>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                @elseif ($tipo === 'textarea')
                    <textarea id="{{ $nombre }}" name="{{ $nombre }}" rows="3" @required($campo['required'] ?? false) class="block w-full p-2.5 text-sm border rounded-lg @error($nombre) border-red-500 @else border-slate-300 @enderror">{{ $valor }}</textarea>
                @elseif ($tipo === 'image')
                    <input id="{{ $nombre }}" name="{{ $nombre }}" type="file" accept="image/png,image/jpeg,image/webp" data-image-preview="image-preview-{{ $nombre }}" @required(($campo['required'] ?? false) && $modo === 'crear') class="block w-full p-2.5 text-sm border rounded-lg @error($nombre) border-red-500 @else border-slate-300 @enderror">
                    <img id="image-preview-{{ $nombre }}" src="{{ app(\App\Services\CloudinaryImageStorage::class)->url($valor) ?? '' }}" alt="Vista previa de {{ strtolower($campo['label']) }}" @class(['hidden' => !$valor, 'mt-3 h-28 w-28 rounded-lg border border-slate-200 object-cover'])>
                @else
                    <input id="{{ $nombre }}" name="{{ $nombre }}" type="{{ $tipo === 'number' ? 'text' : $tipo }}" value="{{ $tipo === 'password' ? '' : $valor }}" @required(($campo['required'] ?? false) && !($modo === 'editar' && $tipo === 'password')) @if ($tipo === 'number') inputmode="{{ in_array('integer', $campo['rules'] ?? [], true) ? 'numeric' : 'decimal' }}" data-numeric-only data-integer="{{ in_array('integer', $campo['rules'] ?? [], true) ? 'true' : 'false' }}" @endif @if (in_array($nombre, ['nombres', 'apellidos', 'contacto'], true)) minlength="2" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s'-]+" title="Usa letras, espacios, guiones o apóstrofes" data-letters-only @endif @if ($tipo === 'password') minlength="8" autocomplete="new-password" @endif class="block w-full p-2.5 text-sm border rounded-lg @error($nombre) border-red-500 @else border-slate-300 @enderror">
                @endif
                @error($nombre)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        @endforeach
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
            <a href="{{ route($modulo . '.listar') }}" class="px-4 py-2 text-sm border border-slate-300 rounded-lg">Cancelar</a>
            <button type="submit" class="px-4 py-2 text-sm text-white bg-amber-500 rounded-lg hover:bg-amber-600">{{ $modo === 'crear' ? 'Validar formulario' : 'Validar actualización' }}</button>
        </div>
    </form>
</div>