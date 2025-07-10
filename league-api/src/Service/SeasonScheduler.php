<?php

namespace App\Service;

use App\Entity\Game;
use App\Repository\TeamRepository;
use App\Schedule\MatchPairGeneratorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class SeasonScheduler
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly MatchPairGeneratorInterface $pairGenerator,
        private readonly EntityManagerInterface $em
    ) {}

    public function generateSchedule(): void
    {
        $teams = new ArrayCollection($this->teamRepository->findAll());
        $pairs = $this->pairGenerator->generate($teams);
        shuffle($pairs);

        $week = 1;
        foreach ($pairs as $pairDto) {
            $game = new Game();
            $game->setHomeTeam($pairDto->getHomeTeam());
            $game->setAwayTeam($pairDto->getAwayTeam());
            $game->setWeek($week++);
            $this->em->persist($game);
        }

        $this->em->flush();
    }
}
