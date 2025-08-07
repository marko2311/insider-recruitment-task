<?php

declare(strict_types=1);

namespace App\Tests\Service\Simulation;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Service\Generator\GameResultGeneratorInterface;
use App\Service\Simulation\StandingUpdater;
use App\Service\Simulation\WeekSimulator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class WeekSimulatorTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSimulateUpdatesUnplayedGames(): void
    {
        // Arrange
        $game1 = $this->createMock(Game::class);
        $game1->method('getHomeGoals')->willReturn(null);
        $game1->method('getAwayGoals')->willReturn(null);

        $game2 = $this->createMock(Game::class);
        $game2->method('getHomeGoals')->willReturn(null);
        $game2->method('getAwayGoals')->willReturn(null);

        $games = [$game1, $game2];

        $gameRepository = $this->createMock(GameRepository::class);
        $gameRepository->expects($this->once())
            ->method('findBy')
            ->with(['week' => 5])
            ->willReturn($games);

        $resultGenerator = $this->createMock(GameResultGeneratorInterface::class);
        $resultGenerator->expects($this->exactly(2))
            ->method('generate')
            ->willReturn([2, 1]);

        $game1->expects($this->once())->method('setHomeGoals')->with(2);
        $game1->expects($this->once())->method('setAwayGoals')->with(1);
        $game2->expects($this->once())->method('setHomeGoals')->with(2);
        $game2->expects($this->once())->method('setAwayGoals')->with(1);

        $persistedGames = [];
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('persist')->willReturnCallback(function ($game) use (&$persistedGames) {
            $persistedGames[] = $game;
        });
        $em->expects($this->once())->method('flush');
        $em->expects($this->once())->method('clear');

        $updatedGames = [];
        $standingUpdater = $this->createMock(StandingUpdater::class);
        $standingUpdater->method('updateAfterGame')->willReturnCallback(function ($game) use (&$updatedGames) {
            $updatedGames[] = $game;
        });

        $simulator = new WeekSimulator($gameRepository, $resultGenerator, $standingUpdater, $em);

        // Act
        $simulator->simulate(5);

        // Assert
        $this->assertSame([$game1, $game2], $persistedGames, 'All unplayed games should be persisted');
        $this->assertSame([$game1, $game2], $updatedGames, 'All unplayed games should be passed to StandingUpdater');
    }

    /**
     * @throws Exception
     */
    public function testSimulateSkipsAlreadyPlayedGames(): void
    {
        // Arrange
        $playedGame = $this->createMock(Game::class);
        $playedGame->method('getHomeGoals')->willReturn(1);
        $playedGame->method('getAwayGoals')->willReturn(1);

        $gameRepository = $this->createMock(GameRepository::class);
        $gameRepository->method('findBy')->willReturn([$playedGame]);

        $resultGenerator = $this->createMock(GameResultGeneratorInterface::class);
        $resultGenerator->expects($this->never())->method('generate');

        $playedGame->expects($this->never())->method('setHomeGoals');
        $playedGame->expects($this->never())->method('setAwayGoals');

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('persist');
        $em->expects($this->once())->method('flush');
        $em->expects($this->once())->method('clear');

        $standingUpdater = $this->createMock(StandingUpdater::class);
        $standingUpdater->expects($this->never())->method('updateAfterGame');

        $simulator = new WeekSimulator($gameRepository, $resultGenerator, $standingUpdater, $em);

        // Act
        $simulator->simulate(1);

        // Assert
        $this->assertTrue(true);
    }
}
