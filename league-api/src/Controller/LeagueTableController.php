<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\LeagueTableService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LeagueTableController extends AbstractController
{
    public function __construct(
        private readonly LeagueTableService $leagueTableService
    ) {}

    #[Route('/api/table', name: 'api_table', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $table = $this->leagueTableService->getCurrentTable();

        return $this->json($table, 200, [], ['groups' => ['table']]);
    }
}
