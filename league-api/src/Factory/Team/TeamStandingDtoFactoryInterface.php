<?php

declare(strict_types=1);

namespace App\Factory\Team;

use App\Dto\Team\TeamStandingDto;
use App\Entity\TeamStanding;

interface TeamStandingDtoFactoryInterface
{
    public function createFromEntity(TeamStanding $standing): TeamStandingDto;
}
