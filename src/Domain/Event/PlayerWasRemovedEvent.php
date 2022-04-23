<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Domain\Event;

use Alejgarciarodriguez\SymfonyDemo\Domain\Player;

final class PlayerWasRemovedEvent implements Event
{
    public function __construct(private Player $player)
    {
    }

    public function player(): Player
    {
        return $this->player;
    }
}
