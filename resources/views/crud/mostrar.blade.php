<div class="max-w-3xl">
    <p class="text-amber-600 font-bold text-xs uppercase tracking-widest mb-1">{{ $modelo }}</p>
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Detalle de {{ $titulo }} #{{ $id }}</h1>
    <dl class="divide-y divide-slate-200 border-y border-slate-200">
        @foreach ($campos as $campo)
            @if (($campo['type'] ?? '') !== 'password')
                @php($valor = $registro[$campo['name']] ?? '—')
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-slate-500">{{ $campo['label'] }}</dt>
                    <dd class="text-sm text-slate-900 sm:col-span-2">
                        @if (($campo['type'] ?? '') === 'image')
                            @if ($valor && $valor !== '—')
                                <img src="{{ app(\App\Services\CloudinaryImageStorage::class)->url($valor) }}" alt="{{ $campo['label'] }}" class="h-40 w-40 rounded-lg border border-slate-200 object-cover">
                            @else
                                <span class="text-slate-400">Sin imagen</span>
                            @endif
                        @else
                            {{ ($campo['type'] ?? '') === 'select' ? ($campo['options'][$valor] ?? $valor) : $valor }}
                        @endif
                    </dd>
                </div>
            @endif
        @endforeach
    </dl>
    <div class="flex gap-3 mt-6">
        <a href="{{ route($modulo . '.listar') }}" class="px-4 py-2 text-sm border border-slate-300 rounded-lg">Volver al listado</a>
        <a href="{{ route($modulo . '.edicion', $id) }}" class="px-4 py-2 text-sm text-white bg-amber-500 rounded-lg">Editar ejemplo</a>
    </div>
</div>