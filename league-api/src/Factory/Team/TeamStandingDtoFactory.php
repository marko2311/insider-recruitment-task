<?php

declare(strict_types=1);

namespace App\Factory\Team;

use App\Dto\Team\TeamStandingDto;
use App\Entity\TeamStanding;

readonly class TeamStandingDtoFactory implements TeamStandingDtoFactoryInterface
{
    public function __construct(
        private TeamDtoFactoryInterface $teamDtoFactory
    ) {}

    public function createFromEntity(TeamStanding $standing): TeamStandingDto
    {
        return new TeamStandingDto(
            team: $this->teamDtoFactory->createFromEntity($standing->getTeam()),
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
