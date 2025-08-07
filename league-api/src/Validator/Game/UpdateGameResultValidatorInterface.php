<?php

declare(strict_types=1);

namespace App\Validator\Game;

use App\Dto\Game\UpdateGameResultDTO;

interface UpdateGameResultValidatorInterface
{
    public function validate(UpdateGameResultDTO $dto): void;
}
