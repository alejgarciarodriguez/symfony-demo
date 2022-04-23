<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Club\CreateClub;

use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;

final class CreateClubHandler
{
    public function __construct(private ClubRepository $clubRepository)
    {
    }

    public function __invoke(CreateClubCommand $command): void
    {
        $club = $this->clubRepository->search($command->name());

        if (null !== $club) {
            return;
        }

        $club = new Club(
            name: $command->name(),
            budget: $command->budget(),
        );

        $this->clubRepository->save($club);
    }
}
