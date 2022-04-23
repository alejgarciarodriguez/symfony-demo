<?php

namespace Alejgarciarodriguez\SymfonyDemo\Infrastructure\Doctrine\ORM;

use Alejgarciarodriguez\SymfonyDemo\Domain\Club;
use Alejgarciarodriguez\SymfonyDemo\Domain\ClubRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineClubRepository extends ServiceEntityRepository implements ClubRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Club::class);
    }

    public function save(Club $club): void
    {
        $this->getEntityManager()->persist($club);
        $this->getEntityManager()->flush();
    }

    public function search(string $name): ?Club
    {
        /** @phpstan-ignore-next-line */
        return $this->findOneBy([
            'name' => $name
        ]);
    }
}
