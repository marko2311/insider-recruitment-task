<?php

declare(strict_types=1);

namespace App\Dto\Team;

use Symfony\Component\Serializer\Attribute\Groups;

readonly class TeamStandingDto
{
    public function __construct(
        private TeamDto $team,
        private int  $played,
        private int  $wins,
        private int  $draws,
        private int  $losses,
        private int  $goalsFor,
        private int  $goalsAgainst,
        private int  $points,
    ) {}

    #[Groups(['table'])]
    public function getGoalDifference(): int
    {
        return $this->goalsFor - $this->goalsAgainst;
    }

    public function getTeam(): TeamDto
    {
        return $this->team;
    }

    #[Groups(['table'])]
    public function getTeamName(): string
    {
        return $this->team->getName();
    }

    #[Groups(['table'])]
    public function getPlayed(): int
    {
        return $this->played;
    }

    #[Groups(['table'])]
    public function getWins(): int
    {
        return $this->wins;
    }

    #[Groups(['table'])]
    public function getDraws(): int
    {
        return $this->draws;
    }

    #[Groups(['table'])]
    public function getLosses(): int
    {
        return $this->losses;
    }

    #[Groups(['table'])]
    public function getGoalsFor(): int
    {
        return $this->goalsFor;
    }

    #[Groups(['table'])]
    public function getGoalsAgainst(): int
    {
        return $this->goalsAgainst;
    }

    #[Groups(['table'])]
    public function getPoints(): int
    {
        return $this->points;
    }
}
