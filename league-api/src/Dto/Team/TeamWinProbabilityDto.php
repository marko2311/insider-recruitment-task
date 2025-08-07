<?php

declare(strict_types=1);

namespace App\Dto\Team;

use Symfony\Component\Serializer\Attribute\Groups;

readonly class TeamWinProbabilityDto
{
    public function __construct(
        #[Groups(['prediction'])]
        private string $teamName,

        #[Groups(['prediction'])]
        private int $finalPoints,

        #[Groups(['prediction'])]
        private float $winProbability,
    ) {}

    public function getTeamName(): string
    {
        return $this->teamName;
    }

    public function getFinalPoints(): int
    {
        return $this->finalPoints;
    }

    public function getWinProbability(): float
    {
        return $this->winProbability;
    }
}
