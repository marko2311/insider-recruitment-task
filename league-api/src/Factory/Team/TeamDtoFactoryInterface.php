<?php

declare(strict_types=1);

namespace App\Factory\Team;

use App\Dto\Team\TeamDto;
use App\Entity\Team;

interface TeamDtoFactoryInterface
{
    public function createFromEntity(Team $team): TeamDto;
}
