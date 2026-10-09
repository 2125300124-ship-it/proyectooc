<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeolocalizacionController extends Controller
{
    public function obtenerUbicacion(Request $request)
    {
        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');
        $locationData = [];
        $weather = null;

        if ($latitude !== null && $longitude !== null) {
            $key = config('services.opencage.key');
            if ($key) {
                $response = Http::get('https://api.opencagedata.com/geocode/v1/json', [
                    'q' => "$latitude,$longitude",
                    'key' => $key,
                    'language' => 'es',
                ]);
                $address = $response->json('results.0.components', []);

                if ($response->successful() && $address) {
                    $locationData = [
                        'city' => $address['city'] ?? $address['town'] ?? $address['village'] ?? null,
                        'state' => $address['state'] ?? $address['state_district'] ?? null,
                        'country' => $address['country'] ?? null,
                    ];
                }
            }

            if (!$locationData && config('services.mapquest.key')) {
                $response = Http::get('https://www.mapquestapi.com/geocoding/v1/reverse', [
                    'key' => config('services.mapquest.key'),
                    'location' => "$latitude,$longitude",
                    'locale' => 'es_MX',
                ]);
                $address = $response->json('results.0.locations.0', []);

                if ($response->successful() && $address) {
                    $locationData = [
                        'city' => $address['adminArea5'] ?? $address['adminArea4'] ?? null,
                        'state' => $address['adminArea3'] ?? null,
                        'country' => $address['adminArea1'] ?? null,
                    ];
                }
            }

            if (!$locationData && config('services.positionstack.key')) {
                $response = Http::get('https://api.positionstack.com/v1/reverse', [
                    'access_key' => config('services.positionstack.key'),
                    'query' => "$latitude,$longitude",
                ]);
                $address = $response->json('data.0', []);

                if ($response->successful() && $address) {
                    $locationData = [
                        'city' => $address['locality'] ?? $address['name'] ?? null,
                        'state' => $address['region'] ?? null,
                        'country' => $address['country'] ?? null,
                    ];
                }
            }

            if (!$locationData) {
                $response = Http::get('https://api.bigdatacloud.net/data/reverse-geocode-client', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'localityLanguage' => 'es',
                ]);

                if ($response->successful()) {
                    $address = $response->json();
                    $locationData = [
                        'city' => $address['city'] ?? $address['locality'] ?? null,
                        'state' => $address['principalSubdivision'] ?? null,
                        'country' => $address['countryName'] ?? null,
                    ];
                }
            }

            $weather = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,relative_humidity_2m',
                'hourly' => 'precipitation_probability',
                'forecast_days' => 1,
                'timezone' => 'auto',
            ]);
        }

        $exchange = Http::get('https://api.frankfurter.dev/v1/latest', [
            'base' => 'USD',
            'symbols' => 'MXN',
        ]);

        if (($weather && $weather->failed()) || $exchange->failed()) {
            return response()->json(['error' => 'No se pudieron obtener los datos de las API.'], 500);
        }

        $weatherData = $weather ? $weather->json() : [];
        $exchangeData = $exchange->json();
        $currentTime = substr($weatherData['current']['time'] ?? '', 0, 13);
        $hours = array_map(
            fn ($time) => substr($time, 0, 13),
            $weatherData['hourly']['time'] ?? []
        );
        $currentHour = array_search($currentTime, $hours, true);

        return response()->json([
            'city' => $locationData['city'] ?? null,
            'state' => $locationData['state'] ?? null,
            'country' => $locationData['country'] ?? null,
            'temperature' => $weatherData['current']['temperature_2m'] ?? null,
            'humidity' => $weatherData['current']['relative_humidity_2m'] ?? null,
            'rainProbability' => $currentHour !== false
                ? ($weatherData['hourly']['precipitation_probability'][$currentHour] ?? null)
                : null,
            'exchangeRate' => $exchangeData['rates']['MXN'] ?? null,
        ]);
    }
}