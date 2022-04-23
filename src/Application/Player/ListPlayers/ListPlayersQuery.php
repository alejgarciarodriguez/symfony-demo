<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Player\ListPlayers;

use Alejgarciarodriguez\SymfonyDemo\Domain\Command;

final class ListPlayersQuery implements Command
{
    public function __construct(
        private string  $sortBy,
        private string  $orderBy,
        private ?string $club,
        private int     $limit,
        private int     $page,
    )
    {
    }

    public function orderBy(): string
    {
        return $this->orderBy;
    }

    public function sortBy(): string
    {
        return $this->sortBy;
    }

    public function club(): ?string
    {
        return $this->club;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function page(): int
    {
        return $this->page;
    }
}
