<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Player\ListPlayers;

use Alejgarciarodriguez\SymfonyDemo\Domain\PlayerRepository;

final class ListPlayersHandler
{
    public function __construct(private PlayerRepository $playerRepository)
    {
    }

    public function __invoke(ListPlayersQuery $query): array
    {
        $players = $this->playerRepository->searchAll(
            sortBy: $query->sortBy(),
            orderBy: $query->orderBy(),
            club: $query->club(),
            limit: $query->limit(),
            page: $query->page(),
        );

        $total = $this->playerRepository->total($query->club());

        $pages = \max(\ceil($total/$query->limit()), 1);

        return [
            'total' => $total,
            'pages' => $pages,
            'limit' => $query->limit(),
            'players' => $players,
        ];
    }
}
