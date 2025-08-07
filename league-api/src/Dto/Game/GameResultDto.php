<?php

declare(strict_types=1);

namespace App\Dto\Game;

use App\Dto\Team\TeamDto;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class GameResultDto
{
    public function __construct(
        private TeamDto $homeTeam,
        private TeamDto $awayTeam,
        private ?int $homeGoals,
        private ?int $awayGoals,
        private int  $week
    ) {}

    public function getHomeTeam(): TeamDto
    {
        return $this->homeTeam;
    }

    #[Groups(['game'])]
    public function getHomeTeamName(): string
    {
        return $this->homeTeam->getName();
    }

    public function getAwayTeam(): TeamDto
    {
        return $this->awayTeam;
    }

    #[Groups(['game'])]
    public function getAwayTeamName(): string
    {
        return $this->awayTeam->getName();
    }

    #[Groups(['game'])]
    public function getHomeGoals(): ?int
    {
        return $this->homeGoals;
    }

    #[Groups(['game'])]
    public function getAwayGoals(): ?int
    {
        return $this->awayGoals;
    }

    #[Groups(['game'])]
    public function getWeek(): int
    {
        return $this->week;
    }

    #[Groups(['game'])]
    public function isPlayed(): bool
    {
        return $this->homeGoals !== null && $this->awayGoals !== null;
    }
}
