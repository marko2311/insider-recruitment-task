<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Team;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class GameResultDto
{
    public function __construct(
        private Team $homeTeam,
        private Team $awayTeam,
        private ?int $homeGoals,
        private ?int $awayGoals,
        private int  $week
    ) {}

    public function getHomeTeam(): Team
    {
        return $this->homeTeam;
    }

    #[Groups(['game'])]
    public function getHomeTeamName(): string
    {
        return $this->homeTeam->getName();
    }

    public function getAwayTeam(): Team
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
