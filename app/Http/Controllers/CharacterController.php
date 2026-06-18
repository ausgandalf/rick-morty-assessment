<?php

namespace App\Http\Controllers;

use App\Exceptions\RickAndMortyApiException;
use App\Http\Requests\CharacterIndexRequest;
use App\Services\RickAndMorty\CharacterService;
use Inertia\Inertia;
use Inertia\Response;

class CharacterController extends Controller
{
    public function __construct(
        private readonly CharacterService $characters
    ) {}

    public function index(CharacterIndexRequest $request): Response
    {
        $filters = $request->validated();

        try {
            $data = $this->characters->list($filters);
        } catch (RickAndMortyApiException|\Throwable) {
            return $this->indexErrorResponse($filters);
        }

        return Inertia::render('Characters/Index', $data);
    }

    private function indexErrorResponse(array $filters): Response
    {
        return Inertia::render('Characters/Index', [
            'characters' => [],
            'meta' => [
                'count' => 0,
                'pages' => 1,
                'next' => null,
                'prev' => null,
                'current_page' => (int) ($filters['page'] ?? 1),
            ],
            'filters' => [
                'name' => $filters['name'] ?? '',
                'status' => $filters['status'] ?? '',
                'species' => $filters['species'] ?? '',
                'gender' => $filters['gender'] ?? '',
                'type' => $filters['type'] ?? '',
            ],
            'error' => 'Sorry, something went wrong, try to search again.',
        ]);
    }

    public function details(int $id): Response
    {
        try {
            $character = $this->characters->find($id);
        } catch (RickAndMortyApiException $e) {
            return Inertia::render('Characters/Details', [
                'character' => null,
                'error' => $e->getStatusCode() === 404
                    ? 'Character not found.'
                    : 'Sorry, something went wrong, try again.',
            ]);
        } catch (\Throwable) {
            return Inertia::render('Characters/Details', [
                'character' => null,
                'error' => 'Sorry, something went wrong, try again.',
            ]);
        }

        return Inertia::render('Characters/Details', [
            'character' => $character,
            'error' => null,
        ]);
    }
}