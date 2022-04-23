<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Player\RemovePlayerClub;

final class RemovePlayerClubCommand
{
    public function __construct(private string $name)
    {
    }

    public function name(): string
    {
        return $this->name;
    }
}
