<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Player\CreatePlayer;

use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\ClubNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Player;
use Alejgarciarodriguez\SymfonyDemo\Domain\PlayerRepository;

final class CreatePlayerHandler
{
    public function __construct(
        private ClubRepository   $clubRepository,
        private PlayerRepository $playerRepository,
    )
    {
    }

    public function __invoke(CreatePlayerCommand $command): void
    {
        $referee = $this->playerRepository->search($command->name());

        if (null !== $referee) {
            return;
        }

        if (null === $command->club()) {
            $this->createPlayerWithNoClub($command);
        } else {
            $this->createPlayerWithClub($command);
        }
    }

    private function createPlayerWithNoClub(CreatePlayerCommand $command): void
    {
        $referee = new Player(
            name: $command->name(),
            salary: $command->salary(),
            club: null,
        );

        $this->playerRepository->save($referee);
    }

    private function createPlayerWithClub(CreatePlayerCommand $command): void
    {
        $club = $this->clubRepository->search($command->club());

        if (null === $club) {
            throw new ClubNotFoundException($command->club());
        }

        $referee = new Player(
            name: $command->name(),
            salary: $command->salary(),
            club: $club,
        );

        $club->addPlayer($referee);

        $this->playerRepository->save($referee);
        $this->clubRepository->save($club);
    }
}
