<?php

namespace App\Services\RickAndMorty;

use App\Exceptions\RickAndMortyApiException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RickAndMortyApiClient
{
    public function isOnline(): bool
    {
        try {
            $url = rtrim(config('rickandmorty.base_url'), '/').'/character/1';

            return Http::timeout(config('rickandmorty.timeout'))
                ->acceptJson()
                ->get($url)
                ->successful();
        } catch (\Throwable) {
            return false;
        }
    }

    public function get(string $path, array $query = []): array
    {
        $cacheKey = 'rm_api_' . md5($path . serialize($query));

        return Cache::remember(
            $cacheKey,
            config('rickandmorty.cache_ttl'),
            fn () => $this->fetch($path, $query)
        );
    }

    private function fetch(string $path, array $query = []): array
    {
        $url = rtrim(config('rickandmorty.base_url'), '/') . '/' . ltrim($path, '/');

        try {
            $response = Http::timeout(config('rickandmorty.timeout'))
                ->acceptJson()
                ->get($url, $query);
        } catch (\Throwable $exception) {
            Log::warning('Rick and Morty API connection error', [
                'url' => $url,
                'message' => $exception->getMessage(),
            ]);

            throw new RickAndMortyApiException('Unable to connect to Rick and Morty API.', 503);
        }

        if ($response->status() === 404) {
            throw new RickAndMortyApiException('Resource not found.', 404);
        }

        if (! $response->successful()) {
            Log::warning('Rick and Morty API error', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RickAndMortyApiException('Unable to fetch data from Rick and Morty API.', $response->status());
        }

        return $response->json();
    }
}