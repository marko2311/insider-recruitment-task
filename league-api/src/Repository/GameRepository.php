<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findByWeek(int $week): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.week = :week')
            ->setParameter('week', $week)
            ->orderBy('g.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByTeam(int $teamId): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.homeTeam = :id OR g.awayTeam = :id')
            ->setParameter('id', $teamId)
            ->getQuery()
            ->getResult();
    }

    public function findAllWeeks(): array
    {
        return $this->createQueryBuilder('g')
            ->select('DISTINCT g.week')
            ->orderBy('g.week', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function findUnplayedGames(): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.homeGoals IS NULL')
            ->andWhere('g.awayGoals IS NULL')
            ->orderBy('g.week', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
