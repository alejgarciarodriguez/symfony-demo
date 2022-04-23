<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Infrastructure\EventBus;

use Alejgarciarodriguez\SymfonyDemo\Domain\Event\Event;
use Alejgarciarodriguez\SymfonyDemo\Domain\EventBus;
use Symfony\Component\Messenger\MessageBusInterface;

final class SymfonyMessengerEventBus implements EventBus
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    public function dispatch(Event $event): void
    {
        $this->bus->dispatch($event);
    }
}
