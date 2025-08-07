<?php

declare(strict_types=1);

namespace App\Factory\Team;

use App\Dto\Team\TeamDto;
use App\Entity\Team;

class TeamDtoFactory implements TeamDtoFactoryInterface
{
    public function createFromEntity(Team $team): TeamDto
    {
        return new TeamDto(
            id: $team->getId(),
            name: $team->getName(),
            strength: $team->getStrength()
        );
    }
}
