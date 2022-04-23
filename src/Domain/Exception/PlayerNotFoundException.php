<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Domain\Exception;

final class PlayerNotFoundException extends ResourceNotFoundException
{
    public function __construct(string $name)
    {
        parent::__construct(\sprintf('Player "%s" not found', $name));
    }
}
