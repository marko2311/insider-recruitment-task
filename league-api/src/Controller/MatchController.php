<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Game;
use App\Factory\Game\GameResultDtoFactoryInterface;
use App\Factory\Game\UpdateGameResultDtoFactoryInterface;
use App\Repository\GameRepository;
use App\Validator\Game\UpdateGameResultValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MatchController extends AbstractController
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly GameResultDtoFactoryInterface $gameResultDtoFactory,
        private readonly UpdateGameResultDtoFactoryInterface $updateGameResultDtoFactory,
        private readonly UpdateGameResultValidatorInterface $updateGameResultValidator,
        private readonly EntityManagerInterface $em
    ) {}

    #[Route('/api/matches/week/{week}', name: 'api_matches_week', methods: ['GET'])]
    public function getMatchesByWeek(int $week): JsonResponse
    {
        $games = $this->gameRepository->findBy(['week' => $week]);

        $results = array_map(
            fn(Game $game) => $this->gameResultDtoFactory->createFromEntity($game),
            $games
        );

        return $this->json($results, Response::HTTP_OK, [], ['groups' => ['game']]);
    }

    #[Route('/api/matches', name: 'api_matches', methods: ['GET'])]
    public function getAllMatches(): JsonResponse
    {
        $games = $this->gameRepository->findAll();

        $results = array_map(
            fn(Game $game) => $this->gameResultDtoFactory->createFromEntity($game),
            $games
        );

        return $this->json($results, Response::HTTP_OK, [], ['groups' => ['game']]);
    }

    #[Route('/api/matches/{id}', name: 'api_match_update', methods: ['PATCH'])]
    public function updateMatch(int $id, Request $request): JsonResponse
    {
        $game = $this->gameRepository->find($id);

        if (!$game) {
            return $this->json(['error' => 'Game not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $dto = $this->updateGameResultDtoFactory->create(
            $data['homeGoals'] ?? null,
            $data['awayGoals'] ?? null
        );

        $this->updateGameResultValidator->validate($dto);

        $game->setHomeGoals($dto->getHomeGoals());
        $game->setAwayGoals($dto->getAwayGoals());

        $this->em->flush();

        return $this->json(
            $this->gameResultDtoFactory->createFromEntity($game),
            Response::HTTP_OK,
            [],
            ['groups' => ['game']]
        );
    }

}
