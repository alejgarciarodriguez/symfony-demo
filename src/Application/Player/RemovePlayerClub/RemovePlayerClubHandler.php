<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Player\RemovePlayerClub;

use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\PlayerNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\PlayerRepository;

final class RemovePlayerClubHandler
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private ClubRepository   $clubRepository,
    )
    {
    }

    public function __invoke(RemovePlayerClubCommand $command): void
    {
        $player = $this->playerRepository->search($command->name());

        if (null === $player) {
            throw new PlayerNotFoundException($command->name());
        }

        $club = $player->getClub();

        if (null === $club) {
            return;
        }

        $club->removePlayer($player);

        $this->clubRepository->save($club);
        $this->playerRepository->save($player);
    }
}
