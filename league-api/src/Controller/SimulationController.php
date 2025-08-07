<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SeasonSimulator;
use App\Service\WeekSimulator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SimulationController extends AbstractController
{
    public function __construct(
        private readonly WeekSimulator $weekSimulator,
        private readonly SeasonSimulator $seasonSimulator
    ) {}

    #[Route('/api/simulate/week/{week}', name: 'api_simulate_week', methods: ['POST'])]
    public function simulateWeek(int $week): JsonResponse
    {
        $this->weekSimulator->simulate($week);

        return $this->json([
            'status' => 200
        ]);
    }

    #[Route('/api/simulate/season', name: 'api_simulate_season', methods: ['POST'])]
    public function simulateSeason(): JsonResponse
    {
        $this->seasonSimulator->simulate();

        return $this->json([
            'status' => 200
        ]);
    }
}
