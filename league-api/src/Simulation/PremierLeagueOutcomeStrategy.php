<?php

declare(strict_types=1);

namespace App\Simulation;

use App\Entity\Game;
use App\Entity\TeamStanding;

class PremierLeagueOutcomeStrategy implements MatchOutcomeStrategyInterface
{
    public function apply(Game $game, TeamStanding $homeStanding, TeamStanding $awayStanding): void
    {
        $homeGoals = $game->getHomeGoals();
        $awayGoals = $game->getAwayGoals();

        if ($homeGoals > $awayGoals) {
            $homeStanding->setWins($homeStanding->getWins() + 1);
            $homeStanding->setPoints($homeStanding->getPoints() + 3);
            $awayStanding->setLosses($awayStanding->getLosses() + 1);
        } elseif ($awayGoals > $homeGoals) {
            $awayStanding->setWins($awayStanding->getWins() + 1);
            $awayStanding->setPoints($awayStanding->getPoints() + 3);
            $homeStanding->setLosses($homeStanding->getLosses() + 1);
        } else {
            $homeStanding->setDraws($homeStanding->getDraws() + 1);
            $awayStanding->setDraws($awayStanding->getDraws() + 1);
            $homeStanding->setPoints($homeStanding->getPoints() + 1);
            $awayStanding->setPoints($awayStanding->getPoints() + 1);
        }
    }
}
