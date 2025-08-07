<?php

declare(strict_types=1);

namespace App\Factory\Match;

use App\Dto\Match\MatchPairDto;
use App\Entity\Team;
use App\Factory\Team\TeamDtoFactoryInterface;

class MatchPairDtoFactory implements MatchPairDtoFactoryInterface
{
    public function __construct(
        private readonly TeamDtoFactoryInterface $teamDtoFactory
    ) {}

    public function create(Team $homeTeam, Team $awayTeam): MatchPairDto
    {
        return new MatchPairDto(
            homeTeam: $this->teamDtoFactory->createFromEntity($homeTeam),
            awayTeam: $this->teamDtoFactory->createFromEntity($awayTeam)
        );
    }
}
