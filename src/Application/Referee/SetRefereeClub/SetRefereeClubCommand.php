<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Referee\SetRefereeClub;

final class SetRefereeClubCommand
{
    public function __construct(
        private string $refereeName,
        private string $clubName,
    )
    {
    }

    public function RefereeName(): string
    {
        return $this->refereeName;
    }

    public function clubName(): string
    {
        return $this->clubName;
    }
}
