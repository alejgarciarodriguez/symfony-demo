<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Referee\GetReferee;

use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\RefereeNotFoundException;
use Alejgarciarodriguez\SymfonyDemo\Domain\Referee;
use Alejgarciarodriguez\SymfonyDemo\Domain\RefereeRepository;

final class GetRefereeHandler
{
    public function __construct(private RefereeRepository $refereeRepository)
    {
    }

    public function __invoke(GetRefereeQuery $query): Referee
    {
        $referee = $this->refereeRepository->search($query->name());

        if (null === $referee) {
            throw new RefereeNotFoundException($query->name());
        }

        return $referee;
    }
}
