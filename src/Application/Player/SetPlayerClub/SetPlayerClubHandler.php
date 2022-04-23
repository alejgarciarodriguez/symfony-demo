<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Player\SetPlayerClub;

use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\ClubNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\PlayerNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\PlayerRepository;

final class SetPlayerClubHandler
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private ClubRepository   $clubRepository,
    )
    {
    }

    public function __invoke(SetPlayerClubCommand $command): void
    {
        $player = $this->playerRepository->search($command->playerName());

        if (null === $player) {
            throw new PlayerNotFoundException($command->playerName());
        }

        $club = $this->clubRepository->search($command->clubName());

        if (null === $club) {
            throw new ClubNotFoundException($command->clubName());
        }

        if (null !== $player->getClub()) {
            $player->getClub()->removePlayer($player);
        }

        $club->addPlayer($player);

        $this->clubRepository->save($club);
        $this->playerRepository->save($player);
    }
}
