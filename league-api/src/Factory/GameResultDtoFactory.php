<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\GameResultDto;
use App\Entity\Game;

class GameResultDtoFactory
{
    public function createFromEntity(Game $game): GameResultDto
    {
        return new GameResultDto(
            homeTeam: $game->getHomeTeam(),
            awayTeam: $game->getAwayTeam(),
            homeGoals: $game->getHomeGoals(),
            awayGoals: $game->getAwayGoals(),
            week: $game->getWeek()
        );
    }
}
