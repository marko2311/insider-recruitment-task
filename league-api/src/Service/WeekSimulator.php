<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\GameRepository;
use App\Service\Generator\GameResultGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;

class WeekSimulator
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly GameResultGeneratorInterface $resultGenerator,
        private readonly StandingUpdater $standingUpdater,
        private readonly EntityManagerInterface $em
    ) {}

    public function simulate(int $week): void
    {
        $games = $this->gameRepository->findBy(['week' => $week]);

        foreach ($games as $game) {
            if ($game->getHomeGoals() !== null && $game->getAwayGoals() !== null) {
                continue;
            }

            [$homeGoals, $awayGoals] = $this->resultGenerator->generate();
            $game->setHomeGoals($homeGoals);
            $game->setAwayGoals($awayGoals);

            $this->em->persist($game);
            $this->standingUpdater->updateAfterGame($game);
        }

        $this->em->flush();
        $this->em->clear();
    }
}
