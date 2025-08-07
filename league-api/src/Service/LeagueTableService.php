<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Team\TeamStandingDto;
use App\Factory\Team\TeamStandingDtoFactoryInterface;
use App\Repository\TeamStandingRepository;

readonly class LeagueTableService
{
    public function __construct(
        private TeamStandingRepository $repository,
        private TeamStandingDtoFactoryInterface $factory
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
