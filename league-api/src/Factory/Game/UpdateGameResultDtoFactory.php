<?php

declare(strict_types=1);

namespace App\Factory\Game;

use App\Dto\Game\UpdateGameResultDTO;

class UpdateGameResultDtoFactory implements UpdateGameResultDtoFactoryInterface
{
    public function create(?int $homeGoals, ?int $awayGoals): UpdateGameResultDTO
    {
        return new UpdateGameResultDTO(
            homeGoals: $homeGoals,
            awayGoals: $awayGoals
        );
    }
}
