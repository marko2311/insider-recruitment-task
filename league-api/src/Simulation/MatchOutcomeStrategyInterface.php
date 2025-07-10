<?php

namespace App\Simulation;

use App\Entity\Game;
use App\Entity\TeamStanding;

interface MatchOutcomeStrategyInterface
{
    public function apply(Game $game, TeamStanding $homeStanding, TeamStanding $awayStanding): void;
}
