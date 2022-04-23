<?php

namespace Alejgarciarodriguez\SymfonyDemo\Infrastructure\Doctrine\ORM;

use Alejgarciarodriguez\SymfonyDemo\Domain\Player;
use Alejgarciarodriguez\SymfonyDemo\Domain\PlayerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PlayerDoctrineRepository extends ServiceEntityRepository implements PlayerRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    public function search(string $name): ?Player
    {
        /** @phpstan-ignore-next-line */
        return $this->findOneBy([
            'name' => $name,
        ]);
    }

    public function save(Player $player): void
    {
        $this->getEntityManager()->persist($player);
        $this->getEntityManager()->flush();
    }

    public function remove(Player $player): void
    {
        $this->getEntityManager()->remove($player);
        $this->getEntityManager()->flush();
    }

    public function total(?string $filterByClub = null): int
    {
        $qb = $this->createQueryBuilder('players')
            ->select('COUNT(players.name)')
        ;

        if (null !== $filterByClub) {
            $qb->join('players.club', 'club')
                ->where('club.name = :name')
                ->setParameter('name', $filterByClub);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function searchAll(
        string  $sortBy = 'salary',
        string  $orderBy = 'desc',
        ?string $club = null,
        int     $limit = 50,
        int     $page = 0,
    ): array
    {
        $qb = $this->createQueryBuilder('players')
            ->orderBy('players.' . $sortBy, $orderBy)
            ->setMaxResults($limit)
            ->setFirstResult($page);

        if (null !== $club) {
            $qb->join('players.club', 'club')
                ->where('club.name = :name')
                ->setParameter('name', $club);
        }

        return $qb->getQuery()->execute();
    }
}
