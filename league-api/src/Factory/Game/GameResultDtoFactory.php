<?php

declare(strict_types=1);

namespace App\Factory\Game;

use App\Dto\Game\GameResultDto;
use App\Entity\Game;
use App\Factory\Team\TeamDtoFactoryInterface;

class GameResultDtoFactory implements GameResultDtoFactoryInterface
{
    public function __construct(
        private readonly TeamDtoFactoryInterface $teamDtoFactory
    ) {}

    public function createFromEntity(Game $game): GameResultDto
    {
        return new GameResultDto(
            homeTeam: $this->teamDtoFactory->createFromEntity($game->getHomeTeam()),
            awayTeam: $this->teamDtoFactory->createFromEntity($game->getAwayTeam()),
            homeGoals: $game->getHomeGoals(),
            awayGoals: $game->getAwayGoals(),
            week: $game->getWeek()
        );
    }
}
