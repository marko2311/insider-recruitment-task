<?php

namespace App\Simulation;

use App\Entity\Game;

class BasicStrengthBasedSimulator implements MatchSimulatorInterface
{
    public function simulate(Game $game): void
    {
        $homeStrength = $game->getHomeTeam()->getStrength();
        $awayStrength = $game->getAwayTeam()->getStrength();

        $total = $homeStrength + $awayStrength;
        $probHome = $homeStrength / $total;
        $rand = mt_rand() / mt_getrandmax();

        if ($rand < $probHome - 0.1) {
            $game->setHomeGoals(rand(1, 3));
            $game->setAwayGoals(rand(0, 1));
        } elseif ($rand > $probHome + 0.1) {
            $game->setHomeGoals(rand(0, 1));
            $game->setAwayGoals(rand(1, 3));
        } else {
            $draw = rand(0, 2);
            $game->setHomeGoals($draw);
            $game->setAwayGoals($draw);
        }
    }
}
