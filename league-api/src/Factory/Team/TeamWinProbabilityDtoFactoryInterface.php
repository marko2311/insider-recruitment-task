<?php

declare(strict_types=1);

namespace App\Factory\Team;

use App\Dto\Team\TeamWinProbabilityDto;

interface TeamWinProbabilityDtoFactoryInterface
{
    public function create(string $teamName, int $finalPoints, float $winProbability): TeamWinProbabilityDto;
}
