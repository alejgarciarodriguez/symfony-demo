<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Club\CreateClub;

final class CreateClubCommand
{
    public function __construct(
        private string $name,
        private float  $budget,
    )
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function budget(): float
    {
        return $this->budget;
    }
}
