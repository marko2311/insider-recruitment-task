<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\TeamStandingDto;
use App\Entity\TeamStanding;

class TeamStandingDtoFactory
{
    public function createFromEntity(TeamStanding $standing): TeamStandingDto
    {
        return new TeamStandingDto(
            team: $standing->getTeam(),
            played: $standing->getPlayed(),
            wins: $standing->getWins(),
            draws: $standing->getDraws(),
            losses: $standing->getLosses(),
            goalsFor: $standing->getGoalsFor(),
            goalsAgainst: $standing->getGoalsAgainst(),
            points: $standing->getPoints()
        );
    }

}
