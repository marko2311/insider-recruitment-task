<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Team;

readonly class MatchPairDto
{
    public function __construct(
        private Team $homeTeam,
        private Team $awayTeam
    ) {}

    public function getHomeTeam(): Team
    {
        return $this->homeTeam;
    }

    public function getAwayTeam(): Team
    {
        return $this->awayTeam;
    }
}
