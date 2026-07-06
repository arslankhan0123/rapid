<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ReverseGeocodingService
{
    public static function reverse(float $lat, float $lng): array
    {
        // Try multiple services with fallback
        $result = self::tryNominatim($lat, $lng);

        // If we didn't get good address details, try another service
        if (self::isResultIncomplete($result)) {
            \Log::info('Nominatim result incomplete, trying Google Maps', [
                'lat' => $lat,
                'lng' => $lng,
                'nominatim_result' => $result
            ]);
            $googleResult = self::tryGoogleMaps($lat, $lng);

            // Merge results - prefer Google if it has better data
            if (!self::isResultIncomplete($googleResult)) {
                $result = $googleResult;
            } else {
                // Merge the best parts of both
                $result = self::mergeBestResults($result, $googleResult);
            }
        }

        return $result;
    }

    private static function isResultIncomplete(array $result): bool
    {
        $address = $result['address'] ?? [];

        // Consider result incomplete if missing critical location info
        return empty($address) ||
            (empty($address['city']) &&
                empty($address['town']) &&
                empty($address['village']) &&
                empty($address['suburb']));
    }

    private static function mergeBestResults(array $nominatim, array $google): array
    {
        $merged = $nominatim;
        $googleAddr = $google['address'] ?? [];
        $nominatimAddr = $nominatim['address'] ?? [];

        // Use Google data to fill in missing fields
        foreach (['city', 'state', 'country', 'postcode'] as $field) {
            if (empty($nominatimAddr[$field]) && !empty($googleAddr[$field])) {
                $merged['address'][$field] = $googleAddr[$field];
            }
        }

        // Use Google's formatted address if Nominatim doesn't have one
        if (empty($merged['display_name']) && !empty($google['display_name'])) {
            $merged['display_name'] = $google['display_name'];
        }

        return $merged;
    }

    private static function tryNominatim(float $lat, float $lng): array
    {
        try {
            // Add delay to respect Nominatim usage policy
            if (cache()->has('nominatim_last_request')) {
                $lastRequest = cache()->get('nominatim_last_request');
                $timeDiff = now()->diffInMilliseconds($lastRequest);
                if ($timeDiff < 1000) { // 1 second minimum between requests
                    usleep((1000 - $timeDiff) * 1000);
                }
            }
            cache()->put('nominatim_last_request', now(), 60);

            $resp = Http::withHeaders([
                'User-Agent' => config('app.name') . ' (' . config('mail.from.address') . ')'
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'jsonv2',
                'lat' => $lat,
                'lon' => $lng,
                'addressdetails' => 1,
                'zoom' => 16, // Reduced from 18 for better coverage
                'namedetails' => 1,
                'extratags' => 1, // Get additional tags
                'accept-language' => 'en' // Force English results
            ]);

            if ($resp->successful()) {
                $data = $resp->json();
                return self::formatNominatimResult($data);
            }

            \Log::warning('Nominatim API returned error', [
                'status' => $resp->status(),
                'body' => $resp->body()
            ]);
            return [];
        } catch (\Exception $e) {
            \Log::error('Nominatim reverse geocoding failed: ' . $e->getMessage(), [
                'lat' => $lat,
                'lng' => $lng
            ]);
            return [];
        }
    }

    private static function formatNominatimResult(array $data): array
    {
        if (empty($data)) return [];

        $address = $data['address'] ?? [];
        $formatted = [];

        // Map Nominatim fields with multiple fallbacks
        $formatted['city'] = $address['city'] ??
            $address['town'] ??
            $address['village'] ??
            $address['municipality'] ??
            $address['suburb'] ??
            $address['neighbourhood'] ??
            $address['quarter'] ?? null;

        $formatted['state'] = $address['state'] ??
            $address['province'] ??
            $address['region'] ??
            $address['county'] ?? null;

        $formatted['country'] = $address['country'] ?? null;
        $formatted['postcode'] = $address['postcode'] ?? null;

        // Additional useful fields
        $formatted['suburb'] = $address['suburb'] ??
            $address['neighbourhood'] ??
            $address['quarter'] ?? null;

        $formatted['road'] = $address['road'] ??
            $address['street'] ?? null;

        return [
            'address' => array_filter($formatted), // Remove null values
            'display_name' => $data['display_name'] ?? null,
            'source' => 'nominatim'
        ];
    }

    private static function tryGoogleMaps(float $lat, float $lng): array
    {
        $apiKey = config('services.google_maps.key');
        if (!$apiKey) {
            \Log::debug('Google Maps API key not configured');
            return [];
        }

        try {
            $resp = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => $lat . ',' . $lng,
                'key' => $apiKey,
                'language' => 'en', // Force English results
                'result_type' => 'street_address|route|neighborhood|locality|sublocality|administrative_area_level_3'
            ]);

            if ($resp->successful()) {
                $data = $resp->json();

                if ($data['status'] !== 'OK' || empty($data['results'])) {
                    \Log::warning('Google Maps API returned no results', [
                        'status' => $data['status'] ?? 'unknown',
                        'error_message' => $data['error_message'] ?? null
                    ]);
                    return [];
                }

                return self::formatGoogleResult($data['results'][0]);
            }

            \Log::warning('Google Maps API request failed', [
                'status' => $resp->status(),
                'body' => $resp->body()
            ]);
            return [];
        } catch (\Exception $e) {
            \Log::error('Google Maps reverse geocoding failed: ' . $e->getMessage(), [
                'lat' => $lat,
                'lng' => $lng
            ]);
            return [];
        }
    }

    private static function formatGoogleResult(array $googleResult): array
    {
        $addressComponents = $googleResult['address_components'] ?? [];
        $address = [];

        foreach ($addressComponents as $component) {
            $types = $component['types'] ?? [];
            $longName = $component['long_name'] ?? '';
            $shortName = $component['short_name'] ?? '';

            // Map Google's types to our fields with priorities
            if (in_array('locality', $types)) {
                $address['city'] = $longName;
            } elseif (in_array('sublocality', $types) || in_array('sublocality_level_1', $types)) {
                if (empty($address['city'])) { // Only use if no locality found
                    $address['city'] = $longName;
                }
                $address['suburb'] = $longName;
            } elseif (in_array('administrative_area_level_3', $types)) {
                if (empty($address['city'])) { // Fallback for city
                    $address['city'] = $longName;
                }
            } elseif (in_array('administrative_area_level_1', $types)) {
                $address['state'] = $longName;
            } elseif (in_array('administrative_area_level_2', $types)) {
                $address['region'] = $longName;
            } elseif (in_array('country', $types)) {
                $address['country'] = $longName;
            } elseif (in_array('postal_code', $types)) {
                $address['postcode'] = $longName;
            } elseif (in_array('neighborhood', $types)) {
                $address['suburb'] = $address['suburb'] ?? $longName;
            } elseif (in_array('route', $types)) {
                $address['road'] = $longName;
            }
        }

        return [
            'address' => array_filter($address),
            'display_name' => $googleResult['formatted_address'] ?? null,
            'source' => 'google'
        ];
    }

    /**
     * Get a human-readable location string with fallbacks
     */
    public static function getDisplayLocation(array $address): string
    {
        $parts = [];

        // City with fallbacks
        $city = $address['city'] ?? $address['suburb'] ?? null;
        if ($city) $parts[] = $city;

        // State/Region
        $state = $address['state'] ?? $address['region'] ?? null;
        if ($state) $parts[] = $state;

        // Country
        if (!empty($address['country'])) {
            $parts[] = $address['country'];
        }

        return implode(', ', $parts) ?: 'Unknown Location';
    }
}
