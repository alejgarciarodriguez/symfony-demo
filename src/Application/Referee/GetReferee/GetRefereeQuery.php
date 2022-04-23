<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Application\Referee\GetReferee;

final class GetRefereeQuery
{
    public function __construct(private string $name)
    {
    }

    public function name(): string
    {
        return $this->name;
    }
}
