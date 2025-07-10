<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Team>
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function findAllOrderedByStrengthDesc(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.strength', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
