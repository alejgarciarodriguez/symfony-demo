<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Player\RemovePlayer;

use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Event\PlayerWasRemovedEvent;
use Alejgarciarodriguez\SymfonyDemo\Domain\EventBus;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\PlayerNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\PlayerRepository;

final class RemovePlayerHandler
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private ClubRepository   $clubRepository,
        private EventBus         $eventBus
    )
    {
    }

    public function __invoke(RemovePlayerCommand $command): void
    {
        $player = $this->playerRepository->search($command->name());

        if (null === $player) {
            throw new PlayerNotFoundException($command->name());
        }

        if (null !== ($club = $player->getClub())) {
            $club->removePlayer($player);
            $this->clubRepository->save($club);
        }

        $this->playerRepository->remove($player);

        $this->eventBus->dispatch(new PlayerWasRemovedEvent($player));
    }
}
