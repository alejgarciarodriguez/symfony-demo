<?php

namespace Alejgarciarodriguez\SymfonyDemo\Domain;

use Alejgarciarodriguez\SymfonyDemo\Infrastructure\Doctrine\ORM\DoctrineRefereeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineRefereeRepository::class)]
class Referee implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'float')]
    private float $salary;

    #[ORM\ManyToOne(targetEntity: Club::class, inversedBy: 'referees')]
    private ?Club $club;

    public function __construct(
        string $name,
        float  $salary,
        ?Club  $club,
    )
    {
        $this->name   = $name;
        $this->salary = $salary;
        $this->club   = $club;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSalary(): float
    {
        return $this->salary;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): void
    {
        $this->club = $club;
    }

    public function jsonSerialize(): array
    {
        return [
            'name'   => $this->name,
            'salary' => $this->salary,
            'club'   => $this->club?->getName(),
        ];
    }
}
