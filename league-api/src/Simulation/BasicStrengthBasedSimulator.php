<?php

declare(strict_types=1);

namespace App\Simulation;

use App\Entity\Game;
use Random\RandomException;

class BasicStrengthBasedSimulator implements MatchSimulatorInterface
{
    private const WIN_GOALS_RANGE = [1, 5];
    private const DRAW_GOALS_RANGE = [0, 5];
    private const DRAW_MARGIN = 0.1;

    /**
     * @throws RandomException
     */
    public function simulate(Game $game): void
    {
        $homeStrength = $game->getHomeTeam()->getStrength();
        $awayStrength = $game->getAwayTeam()->getStrength();

        if ($homeStrength === 0 && $awayStrength === 0) {
            $this->setDrawScore($game);
            return;
        }

        $homeWinProbability = $this->calculateHomeWinProbability($homeStrength, $awayStrength);
        $random = random_int(0, PHP_INT_MAX) / PHP_INT_MAX;

        if ($random < $homeWinProbability - self::DRAW_MARGIN) {
            $this->setHomeWinScore($game);
        } elseif ($random > $homeWinProbability + self::DRAW_MARGIN) {
            $this->setAwayWinScore($game);
        } else {
            $this->setDrawScore($game);
        }
    }

    private function calculateHomeWinProbability(int $homeStrength, int $awayStrength): float
    {
        return $homeStrength / ($homeStrength + $awayStrength);
    }

    /**
     * @throws RandomException
     */
    private function setHomeWinScore(Game $game): void
    {
        $homeGoals = random_int(...self::WIN_GOALS_RANGE);
        $awayGoals = $this->randomLoserGoals($homeGoals);

        $game->setHomeGoals($homeGoals);
        $game->setAwayGoals($awayGoals);
    }

    /**
     * @throws RandomException
     */
    private function setAwayWinScore(Game $game): void
    {
        $awayGoals = random_int(...self::WIN_GOALS_RANGE);
        $homeGoals = $this->randomLoserGoals($awayGoals);

        $game->setHomeGoals($homeGoals);
        $game->setAwayGoals($awayGoals);
    }

    /**
     * @throws RandomException
     */
    private function setDrawScore(Game $game): void
    {
        $drawScore = random_int(...self::DRAW_GOALS_RANGE);
        $game->setHomeGoals($drawScore);
        $game->setAwayGoals($drawScore);
    }

    /**
     * @throws RandomException
     */
    private function randomLoserGoals(int $winnerGoals): int
    {
        return $winnerGoals <= 1 ? 0 : random_int(0, $winnerGoals - 1);
    }
}
