<?php

namespace App\Factory;

use App\Dto\PredictedTeamStandingDto;
use App\Entity\TeamStanding;

class PredictedTeamStandingDtoFactory
{
    public function createFromEntity(TeamStanding $standing): PredictedTeamStandingDto
    {
        return new PredictedTeamStandingDto(
            teamName: $standing->getTeam()->getName(),
            points: $standing->getPoints(),
            played: $standing->getPlayed(),
            wins: $standing->getWins(),
            draws: $standing->getDraws(),
            losses: $standing->getLosses(),
            goalsFor: $standing->getGoalsFor(),
            goalsAgainst: $standing->getGoalsAgainst(),
            goalDifference: $standing->getGoalDifference()
        );
    }
}
