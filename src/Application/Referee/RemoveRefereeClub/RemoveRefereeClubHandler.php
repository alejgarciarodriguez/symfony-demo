<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Referee\RemoveRefereeClub;

use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\RefereeNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\RefereeRepository;

final class RemoveRefereeClubHandler
{
    public function __construct(
        private RefereeRepository $refereeRepository,
        private ClubRepository    $clubRepository,
    )
    {
    }

    public function __invoke(RemoveRefereeClubCommand $command): void
    {
        $referee = $this->refereeRepository->search($command->name());

        if (null === $referee) {
            throw new RefereeNotFoundException($command->name());
        }

        $club = $referee->getClub();

        if (null === $club) {
            return;
        }

        $club->removeReferee($referee);

        $this->clubRepository->save($club);
        $this->refereeRepository->save($referee);
    }
}
