<?php

declare(strict_types=1);

namespace App\Factory\Game;

use App\Dto\Game\GameResultDto;
use App\Entity\Game;

interface GameResultDtoFactoryInterface
{
    public function createFromEntity(Game $game): GameResultDto;
}
