<?php

declare(strict_types=1);

namespace App\Factory\Game;

use App\Dto\Game\UpdateGameResultDTO;

interface UpdateGameResultDtoFactoryInterface
{
    public function create(?int $homeGoals, ?int $awayGoals): UpdateGameResultDTO;
}
