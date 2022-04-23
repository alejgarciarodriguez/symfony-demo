<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Player\GetPlayer;

use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\PlayerNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Player;
use Alejgarciarodriguez\SymfonyDemo\Domain\PlayerRepository;

final class GetPlayerHandler
{
    public function __construct(private PlayerRepository $playerRepository)
    {
    }

    public function __invoke(GetPlayerQuery $query): Player
    {
        $player = $this->playerRepository->search($query->name());

        if (null === $player) {
            throw new PlayerNotFoundException($query->name());
        }

        return $player;
    }
}
