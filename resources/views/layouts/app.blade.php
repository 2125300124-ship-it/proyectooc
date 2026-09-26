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

            <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="{{ url('/inicio') }}" class="text-slate-600 hover:text-amber-600">Catálogo</a>
                <a href="{{ url('/clientes') }}" class="text-slate-600 hover:text-amber-600">Clientes</a>
                <a href="{{ url('/administradores') }}" class="text-slate-600 hover:text-amber-600">Administradores</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 md:p-8">
            @yield('contenido')
        </div>
    </main>
</body>
</html>