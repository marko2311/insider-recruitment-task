<?php

declare(strict_types=1);

namespace App\Simulation;

use App\Entity\Game;

interface MatchSimulatorInterface
{
    public function simulate(Game $game): void;
}
