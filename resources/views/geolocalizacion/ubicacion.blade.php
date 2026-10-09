<section class="max-w-7xl mx-auto px-4 pt-4" aria-label="Información de ubicación y clima">
    <div class="grid grid-cols-2 lg:grid-cols-5 bg-white border border-slate-200 rounded-xl shadow-sm divide-x divide-y lg:divide-y-0 divide-slate-200">
        <div class="col-span-2 lg:col-span-1 p-4">
            <div class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                <i class="bi bi-geo-alt-fill text-amber-500" aria-hidden="true"></i>
                Ubicación
            </div>
            <p id="geo-location" class="mt-1 font-medium text-slate-900">Obteniendo ubicación...</p>
            <p id="geo-status" class="mt-1 text-xs text-slate-500" aria-live="polite">Esperando permiso del navegador</p>
        </div>
        <div class="p-4">
            <div class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                <i class="bi bi-thermometer-half text-amber-500" aria-hidden="true"></i>
                Temperatura
            </div>
            <p id="geo-temperature" class="mt-1 font-medium text-slate-900">--</p>
        </div>
        <div class="p-4">
            <div class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                <i class="bi bi-droplet-half text-sky-600" aria-hidden="true"></i>
                Humedad
            </div>
            <p id="geo-humidity" class="mt-1 font-medium text-slate-900">--</p>
        </div>
        <div class="p-4">
            <div class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                <i class="bi bi-cloud-rain text-sky-600" aria-hidden="true"></i>
                Prob. lluvia
            </div>
            <p id="geo-rain" class="mt-1 font-medium text-slate-900">--</p>
        </div>
        <div class="p-4">
            <div class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                <i class="bi bi-currency-exchange text-emerald-600" aria-hidden="true"></i>
                USD a MXN
            </div>
            <p id="geo-exchange" class="mt-1 font-medium text-slate-900">Consultando...</p>
        </div>
    </div>
</section>

<script>
    const geolocationEndpoint = @json(route('geolocalizacion.obtener'));

    const updateGeolocationText = (id, value) => {
        const element = document.getElementById(id);
        if (element) element.textContent = value;
    };

    async function loadGeolocation(latitude, longitude) {
        const parameters = new URLSearchParams();
        if (latitude !== null && longitude !== null) {
            parameters.set('latitude', latitude);
            parameters.set('longitude', longitude);
        }

        try {
            const response = await fetch(`${geolocationEndpoint}?${parameters}`, {
                headers: { Accept: 'application/json' }
            });
            if (!response.ok) throw new Error('No fue posible consultar los datos');

            const data = await response.json();
            if (latitude !== null && longitude !== null) {
                const place = [data.city, data.state, data.country].filter(Boolean);
                updateGeolocationText('geo-location', place.length ? place.join(', ') : 'No disponible');
                updateGeolocationText('geo-temperature', Number.isFinite(data.temperature) ? `${Math.round(data.temperature)} °C` : 'No disponible');
                updateGeolocationText('geo-humidity', Number.isFinite(data.humidity) ? `${data.humidity}%` : 'No disponible');
                updateGeolocationText('geo-rain', Number.isFinite(data.rainProbability) ? `${Math.round(data.rainProbability)}%` : 'No disponible');
                updateGeolocationText('geo-status', place.length ? 'Ubicación consultada correctamente' : 'No se pudo obtener la dirección');
            }

            updateGeolocationText('geo-exchange', Number.isFinite(data.exchangeRate)
                ? `1 USD = ${new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(data.exchangeRate)}`
                : 'No disponible');
        } catch {
            if (latitude !== null && longitude !== null) {
                updateGeolocationText('geo-location', 'No fue posible consultar la ubicación');
                updateGeolocationText('geo-temperature', 'No disponible');
                updateGeolocationText('geo-humidity', 'No disponible');
                updateGeolocationText('geo-rain', 'No disponible');
                updateGeolocationText('geo-status', 'Error al conectar con el servicio');
            }

            updateGeolocationText('geo-exchange', 'No disponible');
        }
    }

    loadGeolocation(null, null);

    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            ({ coords }) => loadGeolocation(coords.latitude, coords.longitude),
            () => {
                updateGeolocationText('geo-location', 'Ubicación no disponible');
                updateGeolocationText('geo-status', 'Permite el acceso a la ubicación para consultar el clima');
            },
            { enableHighAccuracy: false, timeout: 10000, maximumAge: 300000 }
        );
    } else {
        updateGeolocationText('geo-location', 'Geolocalización no compatible');
        updateGeolocationText('geo-status', 'Abre la aplicación en un contexto seguro (HTTPS o localhost)');
    }
</script>