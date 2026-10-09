<div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-amber-600 font-bold text-xs uppercase tracking-widest mb-1">{{ $modelo }}</p>
        <h1 class="text-2xl font-bold text-slate-900">{{ $titulo }}</h1>
        <p class="text-sm text-slate-500">Registros guardados</p>
    </div>
    <a href="{{ route($modulo . '.formulario') }}" class="px-4 py-2.5 text-sm font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600">Nuevo registro</a>
</div>

@if (session('exito'))
    <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200" role="status">{{ session('exito') }}</div>
@endif

<div class="overflow-x-auto border border-slate-200 rounded-lg">
    <table class="w-full text-sm text-left text-slate-600">
        <thead class="text-xs text-slate-700 uppercase bg-slate-100">
            <tr>
                @foreach ($campos as $campo)
                    @if (($campo['type'] ?? '') !== 'password')
                        <th scope="col" class="px-4 py-3">{{ $campo['label'] }}</th>
                    @endif
                @endforeach
                <th scope="col" class="px-4 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registros as $registro)
                <tr class="bg-white border-b border-slate-100">
                    @foreach ($campos as $campo)
                        @if (($campo['type'] ?? '') !== 'password')
                            @php($valor = $registro[$campo['name']] ?? '—')
                            <td class="px-4 py-3">
                                @if (($campo['type'] ?? '') === 'image')
                                    @if ($valor && $valor !== '—')
                                        <img src="{{ app(\App\Services\CloudinaryImageStorage::class)->url($valor) }}" alt="{{ $campo['label'] }} de registro {{ $registro['id'] }}" loading="lazy" class="h-14 w-14 rounded-md border border-slate-200 object-cover">
                                    @else
                                        <span class="text-slate-400">Sin imagen</span>
                                    @endif
                                @else
                                    {{ ($campo['type'] ?? '') === 'select' ? ($campo['options'][$valor] ?? $valor) : $valor }}
                                @endif
                            </td>
                        @endif
                    @endforeach
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route($modulo . '.mostrar', $registro['id']) }}" class="text-amber-700 hover:underline">Ver</a>
                            <a href="{{ route($modulo . '.edicion', $registro['id']) }}" class="text-amber-700 hover:underline">Editar</a>
                            <form action="{{ route($modulo . '.borrar', $registro['id']) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-700 hover:underline">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ count($campos) + 1 }}" class="px-6 py-12 text-center text-slate-500">No hay ejemplos para mostrar.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>