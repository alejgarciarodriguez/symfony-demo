<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Domain;

use Alejgarciarodriguez\SymfonyDemo\Domain\Event\Event;

interface EventBus
{
    public function dispatch(Event $event): void;
}
