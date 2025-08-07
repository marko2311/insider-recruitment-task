<?php

declare(strict_types=1);

namespace App\Service\Simulation;

use App\Repository\GameRepository;

readonly class SeasonSimulator
{
    public function __construct(
        private GameRepository $gameRepository,
        private WeekSimulator  $weekSimulator
    ) {}

    public function simulate(): void
    {
        foreach ($this->extractWeekNumbers() as $week) {
            $this->weekSimulator->simulate($week);
        }
    }

    /**
     * @return int[]
     */
    private function extractWeekNumbers(): array
    {
        return array_map(
            fn(array $w): int => (int) $w['week'],
            $this->gameRepository->findAllWeeks()
        );
    }
}
