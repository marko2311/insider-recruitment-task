<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Game;
use App\Entity\TeamStanding;
use App\Repository\TeamStandingRepository;
use App\Simulation\MatchOutcomeStrategyInterface;
use Doctrine\ORM\EntityManagerInterface;

class StandingUpdater
{
    public function __construct(
        private readonly TeamStandingRepository $repo,
        private readonly MatchOutcomeStrategyInterface $strategy,
        private readonly EntityManagerInterface $em
    ) {}

    public function updateAfterGame(Game $game): void
    {
        $home = $game->getHomeTeam();
        $away = $game->getAwayTeam();
        $homeGoals = $game->getHomeGoals();
        $awayGoals = $game->getAwayGoals();

        $homeStanding = $this->repo->findOneBy(['team' => $home]) ?? (new TeamStanding())->setTeam($home);
        $awayStanding = $this->repo->findOneBy(['team' => $away]) ?? (new TeamStanding())->setTeam($away);

        $homeStanding
            ->setPlayed($homeStanding->getPlayed() + 1)
            ->setGoalsFor($homeStanding->getGoalsFor() + $homeGoals)
            ->setGoalsAgainst($homeStanding->getGoalsAgainst() + $awayGoals);

        $awayStanding
            ->setPlayed($awayStanding->getPlayed() + 1)
            ->setGoalsFor($awayStanding->getGoalsFor() + $awayGoals)
            ->setGoalsAgainst($awayStanding->getGoalsAgainst() + $homeGoals);

        $this->strategy->apply($game, $homeStanding, $awayStanding);

        $this->em->persist($homeStanding);
        $this->em->persist($awayStanding);
    }
}
