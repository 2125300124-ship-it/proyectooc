<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso administrativo | Collector Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <main class="mx-auto flex min-h-screen max-w-md items-center px-4">
        <section class="w-full rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <a href="{{ route('inicio') }}" class="text-sm font-medium text-amber-700 hover:text-amber-800">
                &larr; Volver al catálogo
            </a>
            <h1 class="mt-6 text-2xl font-bold text-slate-900">Acceso administrativo</h1>
            <p class="mt-2 text-sm text-slate-600">Ingresa con tu usuario y contraseña.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label for="usuario" class="mb-1 block text-sm font-medium">Usuario</label>
                    <input id="usuario" name="usuario" type="text" value="{{ old('usuario') }}" required autofocus
                        autocomplete="username"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                </div>
                <div>
                    <label for="contraseña" class="mb-1 block text-sm font-medium">Contraseña</label>
                    <input id="contraseña" name="contraseña" type="password" required
                        autocomplete="current-password"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                </div>
                <button type="submit"
                    class="w-full rounded-lg bg-amber-500 px-4 py-2.5 font-semibold text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-300">
                    Iniciar sesión
                </button>
            </form>

            <div class="my-6 flex items-center gap-3 text-xs text-slate-400" aria-hidden="true">
                <span class="h-px flex-1 bg-slate-200"></span>
                <span>o continúa con</span>
                <span class="h-px flex-1 bg-slate-200"></span>
            </div>

            <a href="{{ route('auth.google.redirect') }}"
                class="block w-full rounded-lg bg-white px-4 py-2.5 text-center font-semibold text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-300">
                Iniciar sesión con Google
            </a>
        </section>
    </main>
</body>
</html>
