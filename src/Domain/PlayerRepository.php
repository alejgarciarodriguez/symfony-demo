<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Domain;

interface PlayerRepository
{
    public function search(string $name): ?Player;

    public function save(Player $player): void;

    public function remove(Player $player): void;

    public function total(?string $filterByClub = null): int;

    public function searchAll(
        string $sortBy = 'salary',
        string $orderBy = 'desc',
        ?string $club = null,
        int $limit = 50,
        int $page = 0
    ): array;
}
