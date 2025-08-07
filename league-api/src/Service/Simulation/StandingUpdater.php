<?php

declare(strict_types=1);

namespace App\Service\Simulation;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\TeamStanding;
use App\Repository\TeamStandingRepository;
use App\Simulation\MatchOutcomeStrategyInterface;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;

readonly class StandingUpdater
{
    public function __construct(
        private TeamStandingRepository        $repo,
        private MatchOutcomeStrategyInterface $strategy,
        private EntityManagerInterface        $em
    ) {}

    public function updateAfterGame(Game $game): void
    {
        $homeGoals = $game->getHomeGoals();
        $awayGoals = $game->getAwayGoals();

        if ($homeGoals === null || $awayGoals === null) {
            throw new LogicException('Cannot update standings – game not finished.');
        }

        $homeStanding = $this->getOrCreateStanding($game->getHomeTeam());
        $awayStanding = $this->getOrCreateStanding($game->getAwayTeam());

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
        $this->em->flush();
    }

    private function getOrCreateStanding(Team $team): TeamStanding
    {
        return $this->repo->findOneBy(['team' => $team])
            ?? (new TeamStanding())->setTeam($team);
    }
}
