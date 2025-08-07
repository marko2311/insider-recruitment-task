<?php

namespace App\Simulation;

use App\Entity\Game;

class BasicStrengthBasedSimulator implements MatchSimulatorInterface
{
    public function simulate(Game $game): void
    {
        $homeStrength = $game->getHomeTeam()->getStrength();
        $awayStrength = $game->getAwayTeam()->getStrength();

        if ($homeStrength === 0 && $awayStrength === 0) {
            $this->setDrawScore($game);
            return;
        }

        $homeWinProbability = $this->calculateHomeWinProbability($homeStrength, $awayStrength);
        $random = mt_rand() / mt_getrandmax();

        if ($random < $homeWinProbability - 0.1) {
            $this->setHomeWinScore($game);
        } elseif ($random > $homeWinProbability + 0.1) {
            $this->setAwayWinScore($game);
        } else {
            $this->setDrawScore($game);
        }
    }

    private function calculateHomeWinProbability(int $homeStrength, int $awayStrength): float
    {
        return $homeStrength / ($homeStrength + $awayStrength);
    }

    private function setHomeWinScore(Game $game): void
    {
        $game->setHomeGoals(rand(1, 3));
        $game->setAwayGoals(rand(0, 1));
    }

    private function setAwayWinScore(Game $game): void
    {
        $game->setHomeGoals(rand(0, 1));
        $game->setAwayGoals(rand(1, 3));
    }

    private function setDrawScore(Game $game): void
    {
        $drawScore = rand(0, 2);
        $game->setHomeGoals($drawScore);
        $game->setAwayGoals($drawScore);
    }

}
