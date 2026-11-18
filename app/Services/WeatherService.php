<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WeatherService
{
    private const CACHE_KEY = 'weather.forecast';

    private const FALLBACK_CACHE_MINUTES = 10;

    /**
     * Get the current weather and a short daily forecast.
     *
     * @return array{
     *     current: array{temperature: int, icon: string, description: string},
     *     forecast: list<array{label: string, temperature_max: int, icon: string, description: string}>,
     *     location: string,
     *     updated_at: ?string,
     *     is_fallback: bool
     * }
     */
    public function getForecast(): array
    {
        $cached = Cache::get(self::CACHE_KEY);

        if (is_array($cached)) {
            return $cached;
        }

        $forecast = $this->fetchForecast();

        if ($forecast !== null) {
            Cache::put(
                self::CACHE_KEY,
                $forecast,
                now()->addMinutes((int) config('services.weather.cache_minutes', 1440)),
            );

            return $forecast;
        }

        $fallback = $this->fallbackForecast();

        Cache::put(self::CACHE_KEY, $fallback, now()->addMinutes(self::FALLBACK_CACHE_MINUTES));

        return $fallback;
    }

    /**
     * Fetch the forecast from the configured provider.
     *
     * @return array{
     *     current: array{temperature: int, icon: string, description: string},
     *     forecast: list<array{label: string, temperature_max: int, icon: string, description: string}>,
     *     location: string,
     *     updated_at: ?string,
     *     is_fallback: bool
     * }|null
     */
    private function fetchForecast(): ?array
    {
        $query = [
            'latitude' => (float) config('services.weather.latitude', -8.25),
            'longitude' => (float) config('services.weather.longitude', 111.65),
            'current' => 'temperature_2m,weather_code',
            'daily' => 'temperature_2m_max,weather_code',
            'timezone' => (string) config('services.weather.timezone', 'Asia/Jakarta'),
            'forecast_days' => 4,
        ];

        if (filled(config('services.weather.api_key'))) {
            $query['apikey'] = (string) config('services.weather.api_key');
        }

        try {
            $response = Http::timeout((int) config('services.weather.timeout', 5))
                ->acceptJson()
                ->get((string) config('services.weather.base_url'), $query);
        } catch (Throwable $exception) {
            Log::warning('Prakiraan cuaca gagal diambil dari penyedia.', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();
        $current = is_array($data) ? ($data['current'] ?? null) : null;
        $daily = is_array($data) ? ($data['daily'] ?? null) : null;

        if (! is_array($current) || ! is_array($daily)) {
            return null;
        }

        $currentTemperature = $current['temperature_2m'] ?? null;
        $currentCode = $current['weather_code'] ?? null;

        if (! is_numeric($currentTemperature) || ! is_numeric($currentCode)) {
            return null;
        }

        $currentWeather = $this->describeWeatherCode((int) $currentCode);
        $forecast = [];
        $dates = is_array($daily['time'] ?? null) ? $daily['time'] : [];
        $maxTemperatures = is_array($daily['temperature_2m_max'] ?? null) ? $daily['temperature_2m_max'] : [];
        $weatherCodes = is_array($daily['weather_code'] ?? null) ? $daily['weather_code'] : [];

        foreach (array_slice($dates, 0, 4) as $index => $date) {
            if (! isset($maxTemperatures[$index], $weatherCodes[$index])) {
                continue;
            }

            $weather = $this->describeWeatherCode((int) $weatherCodes[$index]);

            $forecast[] = [
                'label' => Carbon::parse((string) $date)->locale('id')->isoFormat('ddd'),
                'temperature_max' => (int) round((float) $maxTemperatures[$index]),
                'icon' => $weather['icon'],
                'description' => $weather['description'],
            ];
        }

        if ($forecast === []) {
            return null;
        }

        return [
            'current' => [
                'temperature' => (int) round((float) $currentTemperature),
                'icon' => $currentWeather['icon'],
                'description' => $currentWeather['description'],
            ],
            'forecast' => $forecast,
            'location' => (string) config('services.weather.location', 'Watulimo, Trenggalek'),
            'updated_at' => now((string) config('services.weather.timezone', 'Asia/Jakarta'))->format('H:i'),
            'is_fallback' => false,
        ];
    }

    /**
     * Get a static fallback forecast so the widget never breaks.
     *
     * @return array{
     *     current: array{temperature: int, icon: string, description: string},
     *     forecast: list<array{label: string, temperature_max: int, icon: string, description: string}>,
     *     location: string,
     *     updated_at: ?string,
     *     is_fallback: bool
     * }
     */
    private function fallbackForecast(): array
    {
        $timezone = (string) config('services.weather.timezone', 'Asia/Jakarta');
        $forecast = [];

        for ($offset = 0; $offset < 4; $offset++) {
            $forecast[] = [
                'label' => now($timezone)->addDays($offset)->locale('id')->isoFormat('ddd'),
                'temperature_max' => 30,
                'icon' => 'fas fa-cloud-sun',
                'description' => 'Berawan',
            ];
        }

        return [
            'current' => [
                'temperature' => 28,
                'icon' => 'fas fa-cloud-sun',
                'description' => 'Berawan',
            ],
            'forecast' => $forecast,
            'location' => (string) config('services.weather.location', 'Watulimo, Trenggalek'),
            'updated_at' => null,
            'is_fallback' => true,
        ];
    }

    /**
     * Map a WMO weather code to an icon and an Indonesian description.
     *
     * @return array{icon: string, description: string}
     */
    private function describeWeatherCode(int $code): array
    {
        return match (true) {
            $code === 0 => ['icon' => 'fas fa-sun', 'description' => 'Cerah'],
            in_array($code, [1, 2], true) => ['icon' => 'fas fa-cloud-sun', 'description' => 'Cerah Berawan'],
            $code === 3 => ['icon' => 'fas fa-cloud', 'description' => 'Berawan'],
            in_array($code, [45, 48], true) => ['icon' => 'fas fa-smog', 'description' => 'Berkabut'],
            $code >= 51 && $code <= 57 => ['icon' => 'fas fa-cloud-rain', 'description' => 'Gerimis'],
            $code >= 61 && $code <= 67 => ['icon' => 'fas fa-cloud-rain', 'description' => 'Hujan'],
            $code >= 71 && $code <= 77 => ['icon' => 'fas fa-snowflake', 'description' => 'Salju'],
            $code >= 80 && $code <= 82 => ['icon' => 'fas fa-cloud-showers-heavy', 'description' => 'Hujan Lokal'],
            $code >= 85 && $code <= 86 => ['icon' => 'fas fa-snowflake', 'description' => 'Salju Lokal'],
            $code >= 95 => ['icon' => 'fas fa-bolt', 'description' => 'Badai Petir'],
            default => ['icon' => 'fas fa-cloud', 'description' => 'Berawan'],
        };
    }
}
