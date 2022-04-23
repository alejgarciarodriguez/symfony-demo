<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Referee\SetRefereeClub;

use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\ClubNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\RefereeNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\RefereeRepository;

final class SetRefereeClubHandler
{
    public function __construct(
        private RefereeRepository $refereeRepository,
        private ClubRepository    $clubRepository,
    )
    {
    }

    public function __invoke(SetRefereeClubCommand $command): void
    {
        $referee = $this->refereeRepository->search($command->RefereeName());

        if (null === $referee) {
            throw new RefereeNotFoundException($command->RefereeName());
        }

        $club = $this->clubRepository->search($command->clubName());

        if (null === $club) {
            throw new ClubNotFoundException($command->clubName());
        }

        if (null !== $referee->getClub()) {
            $referee->getClub()->removeReferee($referee);
        }

        $club->addReferee($referee);

        $this->clubRepository->save($club);
        $this->refereeRepository->save($referee);
    }
}
