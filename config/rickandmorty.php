<?php

return [
    'base_url' => env('RICKANDMORTY_BASE_URL', 'https://rickandmortyapi.com/api'),
    'cache_ttl' => (int) env('RICKANDMORTY_CACHE_TTL', 600),
    'timeout' => 10,
];