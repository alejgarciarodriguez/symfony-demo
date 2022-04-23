<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Club\GetClub;

use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\ClubNotFoundException;

final class GetClubHandler
{
    public function __construct(private ClubRepository $clubRepository)
    {
    }

    public function __invoke(GetClubQuery $query): Club
    {
        $club = $this->clubRepository->search($query->name());

        if (null === $club) {
            throw new ClubNotFoundException($query->name());
        }

        return $club;
    }
}
