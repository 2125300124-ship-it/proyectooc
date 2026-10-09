<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collector Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800">
    <nav class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold text-lg text-slate-900">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500 text-white">
                    <i class="bi bi-star-fill text-sm"></i>
                </span>
                Collector Store
            </a>

            <div class="hidden md:flex flex-wrap items-center justify-end gap-x-4 gap-y-2 text-sm font-medium">
                @auth('admin')
                    <a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-amber-600">Panel</a>
                    <a href="{{ route('productos.listar') }}" class="text-slate-600 hover:text-amber-600">Productos</a>
                    <a href="{{ route('clientes.listar') }}" class="text-slate-600 hover:text-amber-600">Clientes</a>
                    <a href="{{ route('proveedores.listar') }}" class="text-slate-600 hover:text-amber-600">Proveedores</a>
                    <a href="{{ route('categorias.listar') }}" class="text-slate-600 hover:text-amber-600">Categorías</a>
                    <a href="{{ route('administradores.listar') }}" class="text-slate-600 hover:text-amber-600">Administradores</a>
                    <span class="text-slate-500">{{ Auth::guard('admin')->user()->nombres }}</span>
                @else
                    <a href="{{ route('login') }}" class="text-slate-600 hover:text-amber-600">Acceso administrativo</a>
                @endauth
            </div>
        </div>
    </nav>

    @include('geolocalizacion.ubicacion')

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 md:p-8">
            @yield('contenido')
        </div>
    </main>
    <script>
        document.addEventListener('input', (event) => {
            const input = event.target;
            if (!(input instanceof HTMLInputElement)) return;

            if (input.hasAttribute('data-letters-only')) {
                input.value = input.value.replace(/[^\p{L}\p{M}\s'-]/gu, '');
            }

            if (input.hasAttribute('data-numeric-only')) {
                const integerOnly = input.dataset.integer === 'true';
                let value = input.value.replace(integerOnly ? /\D/g : /[^\d.]/g, '');

                if (!integerOnly) {
                    const [whole, ...fraction] = value.split('.');
                    value = whole + (fraction.length ? `.${fraction.join('')}` : '');
                }

                input.value = value;
            }
        });

        document.addEventListener('change', (event) => {
            const input = event.target;
            if (!(input instanceof HTMLInputElement) || !input.dataset.imagePreview) return;

            const preview = document.getElementById(input.dataset.imagePreview);
            if (!preview) return;

            if (preview.dataset.objectUrl) URL.revokeObjectURL(preview.dataset.objectUrl);
            const file = input.files?.[0];
            if (!file) return;

            preview.dataset.objectUrl = URL.createObjectURL(file);
            preview.src = preview.dataset.objectUrl;
            preview.classList.remove('hidden');
        });
    </script>
</body>
</html>