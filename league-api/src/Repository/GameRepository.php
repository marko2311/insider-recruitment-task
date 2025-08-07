<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
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
