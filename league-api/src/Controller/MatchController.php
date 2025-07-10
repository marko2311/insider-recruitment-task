<?php

namespace App\Controller;

use App\Entity\Game;
use App\Factory\GameResultDtoFactory;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MatchController extends AbstractController
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly GameResultDtoFactory $gameResultDtoFactory,
        private readonly EntityManagerInterface $em
    ) {}

    #[Route('/api/matches/week/{week}', name: 'api_matches_week', methods: ['GET'])]
    public function getWeek(int $week): JsonResponse
    {
        $games = $this->gameRepository->findBy(['week' => $week]);

        $results = array_map(
            fn(Game $game) => $this->gameResultDtoFactory->createFromEntity($game),
            $games
        );

        return $this->json($results, 200, [], ['groups' => ['game']]);
    }

    #[Route('/api/matches', name: 'api_matches_season', methods: ['GET'])]
    public function getSeason(): JsonResponse
    {
        $games = $this->gameRepository->findAll();

        $results = array_map(
            fn(Game $game) => $this->gameResultDtoFactory->createFromEntity($game),
            $games
        );

        return $this->json($results, 200, [], ['groups' => ['game']]);
    }

    #[Route('/api/match/{id}', name: 'api_match_update', methods: ['PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $game = $this->gameRepository->find($id);

        if (!$game) {
            return $this->json(['error' => 'Game not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $homeGoals = $data['homeGoals'] ?? null;
        $awayGoals = $data['awayGoals'] ?? null;

        if (!is_int($homeGoals) || !is_int($awayGoals)) {
            return $this->json(['error' => 'Invalid goals format'], 400);
        }

        $game->setHomeGoals($homeGoals);
        $game->setAwayGoals($awayGoals);

        $this->em->flush();

        return $this->json(['status' => 'Match updated']);
    }
}
