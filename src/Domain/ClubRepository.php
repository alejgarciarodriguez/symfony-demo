<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Domain;

interface ClubRepository
{
    public function search(string $name): ?Club;

    public function save(Club $club): void;
}
