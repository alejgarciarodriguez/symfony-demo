<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Domain\Exception;

final class InsufficientClubBudgetException extends \InvalidArgumentException
{
    public function __construct(string $name)
    {
        parent::__construct(\sprintf('Insufficient budget for club "%s"', $name));
    }
}
