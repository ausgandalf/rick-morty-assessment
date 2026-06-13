<?php

namespace App\Services\RickAndMorty;

class CharacterService
{
    public function __construct(
        private readonly RickAndMortyApiClient $client
    ) {}

    public function list(array $filters): array
    {
        $query = array_filter([
            'page' => $filters['page'] ?? 1,
            'name' => $filters['name'] ?? null,
            'status' => $filters['status'] ?? null,
            'species' => $filters['species'] ?? null,
            'gender' => $filters['gender'] ?? null,
            'type' => $filters['type'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');

        $data = $this->client->get('character', $query);

        return [
            'characters' => collect($data['results'] ?? [])->map(fn ($item) => $this->transformListItem($item))->values(),
            'meta' => [
                'count' => $data['info']['count'] ?? 0,
                'pages' => $data['info']['pages'] ?? 1,
                'next' => $data['info']['next'] ?? null,
                'prev' => $data['info']['prev'] ?? null,
                'current_page' => (int) ($filters['page'] ?? 1),
            ],
            'filters' => [
                'name' => $filters['name'] ?? '',
                'status' => $filters['status'] ?? '',
                'species' => $filters['species'] ?? '',
                'gender' => $filters['gender'] ?? '',
                'type' => $filters['type'] ?? '',
            ],
        ];
    }

    public function find(int $id): array
    {
        $character = $this->client->get("character/{$id}");

        return [
            'id' => $character['id'],
            'name' => $character['name'],
            'status' => $character['status'],
            'species' => $character['species'],
            'type' => $character['type'] ?? '',
            'gender' => $character['gender'] ?? '',
            'image' => $character['image'],
            'origin' => $character['origin']['name'] ?? 'Unknown',
            'location' => $character['location']['name'] ?? 'Unknown',
            'episodes' => $this->resolveEpisodes($character['episode'] ?? []),
        ];
    }

    private function transformListItem(array $character): array
    {
        return [
            'id' => $character['id'],
            'name' => $character['name'],
            'status' => $character['status'],
            'species' => $character['species'],
            'gender' => $character['gender'],
            'image' => $character['image'],
            'type' => $character['type'],
        ];
    }

    private function resolveEpisodes(array $episodeUrls): array
    {
        $episodes = [];

        foreach ($episodeUrls as $url) {
            if (! preg_match('/\/episode\/(\d+)$/', $url, $matches)) {
                continue;
            }

            $episode = $this->client->get('episode/' . $matches[1]);
            $episodes[] = [
                'id' => $episode['id'],
                'name' => $episode['name'],
                'episode' => $episode['episode'],
            ];
        }

        return $episodes;
    }
}