<?php

declare(strict_types=1);

namespace App\Service\Schedule;

use App\Dto\Match\MatchPairDto;
use App\Entity\Game;
use App\Entity\Team;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

readonly class SeasonScheduler
{
    public function __construct(
        private TeamRepository              $teamRepository,
        private MatchPairGeneratorInterface $pairGenerator,
        private EntityManagerInterface      $em
    ) {}

    public function generateSchedule(): void
    {
        /** @var Team[] $teams */
        $teams = $this->teamRepository->findAll();

        $pairs = $this->pairGenerator->generate(new ArrayCollection($teams));

        shuffle($pairs);

        $week = 1;

        /** @var MatchPairDto[] $weeklyPairs */
        foreach ($pairs as $weeklyPairs) {
            foreach ($weeklyPairs as $pairDto) {
                $game = new Game();
                $game->setHomeTeam($pairDto->getHomeTeam());
                $game->setAwayTeam($pairDto->getAwayTeam());
                $game->setWeek($week);

                $this->em->persist($game);
            }
            $week++;
        }

        $this->em->flush();
    }
}
