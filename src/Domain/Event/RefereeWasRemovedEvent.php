<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Domain\Event;

use Alejgarciarodriguez\SymfonyDemo\Domain\Referee;

final class RefereeWasRemovedEvent implements Event
{
    public function __construct(private Referee $referee)
    {
    }

    public function referee(): Referee
    {
        return $this->referee;
    }
}
