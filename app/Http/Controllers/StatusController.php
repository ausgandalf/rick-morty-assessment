<?php

namespace App\Http\Controllers;

use App\Services\RickAndMorty\RickAndMortyApiClient;
use Illuminate\Http\JsonResponse;

class StatusController extends Controller
{
    public function __construct(
        private readonly RickAndMortyApiClient $client
    ) {}

    public function __invoke(): JsonResponse
    {
        return response()->json([
            'online' => $this->client->isOnline(),
        ]);
    }
}
