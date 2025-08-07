<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\PredictionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PredictionController extends AbstractController
{
    #[Route('/api/predict/chances', name: 'api_predict_chances', methods: ['GET'])]
    public function predictChampionChances(PredictionService $service): JsonResponse
    {
        $result = $service->calculateChampionProbabilities();

        return $this->json($result, 200, [], ['groups' => ['prediction']]);
    }
}
