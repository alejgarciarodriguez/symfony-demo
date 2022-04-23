<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Domain\Exception;

final class ClubNotFoundException extends ResourceNotFoundException
{
    public function __construct(string $name)
    {
        parent::__construct(\sprintf('Club "%s" not found', $name));
    }
}
