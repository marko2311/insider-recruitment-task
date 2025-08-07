<?php

declare(strict_types=1);

namespace App\Factory\Team;

use App\Dto\Team\TeamWinProbabilityDto;

class TeamWinProbabilityDtoFactory implements TeamWinProbabilityDtoFactoryInterface
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
