<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\TeamStandingDto;
use App\Factory\TeamStandingDtoFactory;
use App\Repository\TeamStandingRepository;

class LeagueTableService
{
    public function __construct(
        private readonly TeamStandingRepository $repository,
        private readonly TeamStandingDtoFactory $factory
    ) {}

    /**
     * @return TeamStandingDto[]
     */
    public function getCurrentTable(): array
    {
        $entities = $this->repository->findAll();

        $dtos = array_map(
            fn($standing) => $this->factory->createFromEntity($standing),
            $entities
        );

        usort($dtos, function (TeamStandingDto $a, TeamStandingDto $b): int {
            return [
                    $b->getPoints(),
                    $b->getGoalDifference(),
                    $b->getGoalsFor()
                ] <=> [
                    $a->getPoints(),
                    $a->getGoalDifference(),
                    $a->getGoalsFor()
                ];
        });

        return $dtos;
    }
}
