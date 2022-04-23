<?php

namespace Alejgarciarodriguez\SymfonyDemo\Infrastructure\Doctrine\ORM;

use Alejgarciarodriguez\SymfonyDemo\Domain\Referee;
use Alejgarciarodriguez\SymfonyDemo\Domain\RefereeRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineRefereeRepository extends ServiceEntityRepository implements RefereeRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Referee::class);
    }

    public function search(string $name): ?Referee
    {
        /** @phpstan-ignore-next-line */
        return $this->findOneBy([
            'name' => $name,
        ]);
    }

    public function save(Referee $referee): void
    {
        $this->getEntityManager()->persist($referee);
        $this->getEntityManager()->flush();
    }

    public function remove(Referee $referee): void
    {
        $this->getEntityManager()->remove($referee);
        $this->getEntityManager()->flush();
    }
}
