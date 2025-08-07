<?php

declare(strict_types=1);

namespace App\Simulation;

use App\Entity\Game;
use App\Entity\TeamStanding;

class PremierLeagueOutcomeStrategy implements MatchOutcomeStrategyInterface
{
    public function apply(Game $game, TeamStanding $homeStanding, TeamStanding $awayStanding): void
    {
        $goalDiff = $game->getHomeGoals() - $game->getAwayGoals();

        match (true) {
            $goalDiff > 0 => $this->applyWin($homeStanding, $awayStanding),
            $goalDiff < 0 => $this->applyWin($awayStanding, $homeStanding),
            default       => $this->applyDraw($homeStanding, $awayStanding),
        };
    }

    private function applyWin(TeamStanding $winner, TeamStanding $loser): void
    {
        $winner->setWins($winner->getWins() + 1);
        $winner->setPoints($winner->getPoints() + 3);
        $loser->setLosses($loser->getLosses() + 1);
    }

    private function applyDraw(TeamStanding $home, TeamStanding $away): void
    {
        $home->setDraws($home->getDraws() + 1);
        $away->setDraws($away->getDraws() + 1);
        $home->setPoints($home->getPoints() + 1);
        $away->setPoints($away->getPoints() + 1);
    }
}
