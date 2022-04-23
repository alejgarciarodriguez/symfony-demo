<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Player\CreatePlayer;

final class CreatePlayerCommand
{
    public function __construct(
        private string  $name,
        private float   $salary,
        private ?string $club,
    )
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function salary(): float
    {
        return $this->salary;
    }

    public function club(): ?string
    {
        return $this->club;
    }
}
