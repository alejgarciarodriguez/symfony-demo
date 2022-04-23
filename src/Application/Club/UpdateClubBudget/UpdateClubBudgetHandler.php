<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Club\UpdateClubBudget;

use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\ClubNotFoundException;

final class UpdateClubBudgetHandler
{
    public function __construct(private ClubRepository $clubRepository)
    {
    }

    public function __invoke(UpdateClubBudgetCommand $command): void
    {
        $club = $this->clubRepository->search($command->name());

        if (null === $club) {
            throw new ClubNotFoundException($command->name());
        }

        $club->allocateNewBudget($command->budget());

        $this->clubRepository->save($club);
    }
}
