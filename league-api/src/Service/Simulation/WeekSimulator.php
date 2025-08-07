<?php

declare(strict_types=1);

namespace App\Service\Simulation;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Service\Generator\GameResultGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class WeekSimulator
{
    public function __construct(
        private GameRepository               $gameRepository,
        private GameResultGeneratorInterface $resultGenerator,
        private StandingUpdater              $standingUpdater,
        private EntityManagerInterface       $em
    ) {}

    public function simulate(int $week): void
    {
        $games = $this->gameRepository->findBy(['week' => $week]);

        foreach ($games as $game) {
            if ($this->isAlreadyPlayed($game)) {
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

    private function isAlreadyPlayed(Game $game): bool
    {
        return $game->getHomeGoals() !== null && $game->getAwayGoals() !== null;
    }
}
