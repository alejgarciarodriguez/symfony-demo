<?php

declare(strict_types=1);

namespace Alejgarciarodriguez\SymfonyDemo\Domain;

interface RefereeRepository
{
    public function search(string $name): ?Referee;

    public function save(Referee $referee): void;

    public function remove(Referee $referee): void;
}
