<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\TeamWinProbabilityDto;

class TeamWinProbabilityDtoFactory
{
    public function create(string $teamName, int $finalPoints, float $winProbability): TeamWinProbabilityDto
    {
        return new TeamWinProbabilityDto(
            teamName: $teamName,
            finalPoints: $finalPoints,
            winProbability: $winProbability
        );
    }
}
