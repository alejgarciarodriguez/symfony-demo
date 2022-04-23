<?php

namespace Alejgarciarodriguez\SymfonyDemo\Domain;

use Alejgarciarodriguez\SymfonyDemo\Infrastructure\Doctrine\ORM\PlayerDoctrineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerDoctrineRepository::class)]
class Player implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'float')]
    private float $salary;

    #[ORM\ManyToOne(targetEntity: Club::class, inversedBy: 'players')]
    private ?Club $club;

    public function __construct(
        string $name,
        float $salary,
        ?Club $club
    )
    {
        $this->name = $name;
        $this->salary = $salary;
        $this->club = $club;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): self
    {
        $this->club = $club;

        return $this;
    }

    public function getSalary(): float
    {
        return $this->salary;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'salary' => $this->salary,
            'club' => $this->club?->getName()
        ];
    }
}
