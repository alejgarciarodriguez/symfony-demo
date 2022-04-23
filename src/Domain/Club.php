<?php

namespace Alejgarciarodriguez\SymfonyDemo\Domain;

use Alejgarciarodriguez\SymfonyDemo\Domain\Exception\InsufficientClubBudgetException;
use Alejgarciarodriguez\SymfonyDemo\Infrastructure\Doctrine\ORM\DoctrineClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineClubRepository::class)]
class Club implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'string')]
    private string $name;

    #[ORM\Column(type: 'float')]
    private float $budget;

    #[ORM\OneToMany(mappedBy: 'club', targetEntity: Player::class)]
    private Collection $players;

    #[ORM\OneToMany(mappedBy: 'club', targetEntity: Referee::class)]
    private Collection $referees;

    public function __construct(string $name, float $budget)
    {
        $this->name     = $name;
        $this->budget   = $budget;
        $this->players  = new ArrayCollection();
        $this->referees = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBudget(): float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): void
    {
        $this->budget = $budget;
    }

    public function reduceBudget(float $salary): void
    {
        if ($this->budget < $salary) {
            throw new InsufficientClubBudgetException($this->name);
        }

        $this->budget -= $salary;
    }

    public function increaseBudget(float $salary): void
    {
        $this->budget += $salary;
    }

    /**
     * @return Collection<Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
            $this->reduceBudget($player->getSalary());
            $player->setClub($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->removeElement($player)) {
            if ($player->getClub() === $this) {
                $this->increaseBudget($player->getSalary());
                $player->setClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<Referee>
     */
    public function getReferees(): Collection
    {
        return $this->referees;
    }

    public function addReferee(Referee $referee): self
    {
        if (!$this->referees->contains($referee)) {
            $this->referees[] = $referee;
            $this->reduceBudget($referee->getSalary());
            $referee->setClub($this);
        }

        return $this;
    }

    public function removeReferee(Referee $referee): self
    {
        if ($this->referees->removeElement($referee)) {
            if ($referee->getClub() === $this) {
                $this->increaseBudget($referee->getSalary());
                $referee->setClub(null);
            }
        }

        return $this;
    }

    public function getTotalSalaries(): float
    {
        $salaries = 0;

        foreach ($this->players as $player) {
            $salaries += $player->getSalary();
        }

        foreach ($this->referees as $referee) {
            $salaries += $referee->getSalary();
        }

        return $salaries;
    }

    public function allocateNewBudget(float $newBudget): void
    {
        if ($newBudget < $this->getTotalSalaries()) {
            throw new InsufficientClubBudgetException(name: $this->name);
        }

        $this->budget = $newBudget;
    }

    public function jsonSerialize(): array
    {
        return [
            'name'     => $this->getName(),
            'budget'   => $this->budget,
            'players'  => $this->players->toArray(),
            'referees' => $this->referees->toArray(),
        ];
    }
}
