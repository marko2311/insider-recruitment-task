<?php

namespace App\Service;

use App\Repository\GameRepository;

class SeasonSimulator
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly WeekSimulator $weekSimulator
    ) {}

    public function simulate(): void
    {
        $weeks = $this->gameRepository->findAllWeeks();

        foreach ($weeks as $week) {
            $this->weekSimulator->simulate((int) $week['week']);
        }
    }
}
