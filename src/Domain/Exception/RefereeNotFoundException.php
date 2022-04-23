<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Domain\Exception;

final class RefereeNotFoundException extends ResourceNotFoundException
{
    public function __construct(string $name)
    {
        parent::__construct(\sprintf('Referee "%s" not found', $name));
    }
}
