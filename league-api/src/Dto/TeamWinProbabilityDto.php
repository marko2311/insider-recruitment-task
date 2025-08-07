<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;

readonly class TeamWinProbabilityDto
{
    public function __construct(
        #[Groups(['prediction'])]
        public string $teamName,

        #[Groups(['prediction'])]
        public int $finalPoints,

        #[Groups(['prediction'])]
        public float $winProbability,
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
