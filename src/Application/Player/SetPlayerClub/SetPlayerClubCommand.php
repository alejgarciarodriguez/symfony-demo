<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Player\SetPlayerClub;

final class SetPlayerClubCommand
{
    public function __construct(
        private string $playerName,
        private string $clubName,
    )
    {
    }

    public function playerName(): string
    {
        return $this->playerName;
    }

    public function clubName(): string
    {
        return $this->clubName;
    }
}
