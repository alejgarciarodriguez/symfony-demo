<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Referee\RemoveRefereeClub;

final class RemoveRefereeClubCommand
{
    public function __construct(private string $name)
    {
    }

    public function name(): string
    {
        return $this->name;
    }
}
