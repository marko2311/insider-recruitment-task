<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\TeamStanding;
use App\Factory\TeamWinProbabilityDtoFactory;
use App\Repository\GameRepository;
use App\Repository\TeamStandingRepository;
use App\Service\PredictionService;
use App\Simulation\MatchOutcomeStrategyInterface;
use App\Simulation\MatchSimulatorInterface;
use PHPUnit\Framework\TestCase;

class PredictionServiceTest extends TestCase
{
    private GameRepository $gameRepository;
    private TeamStandingRepository $standingRepository;
    private MatchSimulatorInterface $simulator;
    private MatchOutcomeStrategyInterface $outcomeStrategy;
    private TeamWinProbabilityDtoFactory $dtoFactory;
    private PredictionService $service;

    protected function setUp(): void
    {
        $this->gameRepository = $this->createMock(GameRepository::class);
        $this->standingRepository = $this->createMock(TeamStandingRepository::class);
        $this->simulator = $this->createMock(MatchSimulatorInterface::class);
        $this->outcomeStrategy = $this->createMock(MatchOutcomeStrategyInterface::class);
        $this->dtoFactory = $this->createMock(TeamWinProbabilityDtoFactory::class);

        $this->service = new PredictionService(
            $this->gameRepository,
            $this->standingRepository,
            $this->simulator,
            $this->outcomeStrategy,
            $this->dtoFactory
        );
    }

    public function testApplyGameResultModifiesStandings(): void
    {
        $home = (new Team())->setName('Home')->setStrength(70);
        $away = (new Team())->setName('Away')->setStrength(60);

        $game = (new Game())
            ->setHomeTeam($home)
            ->setAwayTeam($away)
            ->setHomeGoals(2)
            ->setAwayGoals(1)
            ->setWeek(1);

        $homeStanding = (new TeamStanding())->setTeam($home);
        $awayStanding = (new TeamStanding())->setTeam($away);

        $this->outcomeStrategy
            ->expects($this->once())
            ->method('apply')
            ->with($game, $homeStanding, $awayStanding);

        $ref = new \ReflectionClass(PredictionService::class);
        $method = $ref->getMethod('applyGameResult');
        $method->setAccessible(true);
        $method->invoke($this->service, $game, $homeStanding, $awayStanding);

        $this->assertEquals(1, $homeStanding->getPlayed());
        $this->assertEquals(2, $homeStanding->getGoalsFor());
        $this->assertEquals(1, $homeStanding->getGoalsAgainst());

        $this->assertEquals(1, $awayStanding->getPlayed());
        $this->assertEquals(1, $awayStanding->getGoalsFor());
        $this->assertEquals(2, $awayStanding->getGoalsAgainst());
    }
}
