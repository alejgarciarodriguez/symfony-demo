<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Referee\CreateReferee;

use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\ClubNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Referee;
use Alejgarciarodriguez\SymfonyDemo\Domain\RefereeRepository;

final class CreateRefereeHandler
{
    public function __construct(
        private ClubRepository    $clubRepository,
        private RefereeRepository $refereeRepository,
    )
    {
    }

    public function __invoke(CreateRefereeCommand $command): void
    {
        $referee = $this->refereeRepository->search($command->name());

        if (null !== $referee) {
            return;
        }

        if (null === $command->club()) {
            $this->createRefereeWithNoClub($command);
        } else {
            $this->createRefereeWithClub($command);
        }
    }

    private function createRefereeWithNoClub(CreateRefereeCommand $command): void
    {
        $referee = new Referee(
            name: $command->name(),
            salary: $command->salary(),
            club: null,
        );

        $this->refereeRepository->save($referee);
    }

    private function createRefereeWithClub(CreateRefereeCommand $command): void
    {
        $club = $this->clubRepository->search($command->club());

        if (null === $club) {
            throw new ClubNotFoundException($command->club());
        }

        $referee = new Referee(
            name: $command->name(),
            salary: $command->salary(),
            club: $club,
        );

        $club->addReferee($referee);

        $this->refereeRepository->save($referee);
        $this->clubRepository->save($club);
    }
}
