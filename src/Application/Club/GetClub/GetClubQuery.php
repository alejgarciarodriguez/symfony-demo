<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Club\GetClub;

final class GetClubQuery
{
    public function __construct(private string $name)
    {
    }

    public function name(): string
    {
        return $this->name;
    }
}
