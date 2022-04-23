<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Referee\RemoveReferee;

use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Event\RefereeWasRemovedEvent;
use Alejgarciarodriguez\SymfonyDemo\Domain\EventBus;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\RefereeNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\RefereeRepository;

final class RemoveRefereeHandler
{
    public function __construct(
        private RefereeRepository $refereeRepository,
        private ClubRepository    $clubRepository,
        private EventBus          $eventBus,
    )
    {
    }

    public function __invoke(RemoveRefereeCommand $command): void
    {
        $referee = $this->refereeRepository->search($command->name());

        if (null === $referee) {
            throw new RefereeNotFoundException($command->name());
        }

        if (null !== ($club = $referee->getClub())) {
            $club->removeReferee($referee);
            $this->clubRepository->save($club);
        }

        $this->refereeRepository->remove($referee);

        $this->eventBus->dispatch(new RefereeWasRemovedEvent($referee));
    }
}
