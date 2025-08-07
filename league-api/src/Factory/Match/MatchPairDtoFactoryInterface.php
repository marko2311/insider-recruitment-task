<?php

declare(strict_types=1);

namespace App\Factory\Match;

use App\Dto\Match\MatchPairDto;
use App\Entity\Team;

interface MatchPairDtoFactoryInterface
{
    public function create(Team $homeTeam, Team $awayTeam): MatchPairDto;
}
