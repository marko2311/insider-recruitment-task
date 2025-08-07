<?php

declare(strict_types=1);

namespace App\Tests\Service\Schedule;

use App\Dto\Match\MatchPairDto;
use App\Entity\Game;
use App\Entity\Team;
use App\Repository\TeamRepository;
use App\Service\Schedule\MatchPairGeneratorInterface;
use App\Service\Schedule\SeasonScheduler;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class SeasonSchedulerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGenerateSchedulePersistsCorrectGames(): void
    {
        // Arrange
        $teamA = $this->createMock(Team::class);
        $teamB = $this->createMock(Team::class);
        $teamC = $this->createMock(Team::class);
        $teamD = $this->createMock(Team::class);

        $teams = [$teamA, $teamB, $teamC, $teamD];

        $week1 = [
            new MatchPairDto($teamA, $teamB),
            new MatchPairDto($teamC, $teamD),
        ];

        $week2 = [
            new MatchPairDto($teamA, $teamC),
            new MatchPairDto($teamB, $teamD),
        ];

        $weeklyPairs = [$week1, $week2];

        $teamRepo = $this->createMock(TeamRepository::class);
        $teamRepo->method('findAll')->willReturn($teams);

        $pairGenerator = $this->createMock(MatchPairGeneratorInterface::class);
        $pairGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($this->isInstanceOf(ArrayCollection::class))
            ->willReturn($weeklyPairs);

        $persistedGames = [];

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('persist')->willReturnCallback(function ($entity) use (&$persistedGames) {
            if ($entity instanceof Game) {
                $persistedGames[] = $entity;
            }
        });
        $em->expects($this->once())->method('flush');

        // Act
        $scheduler = new SeasonScheduler($teamRepo, $pairGenerator, $em);
        $scheduler->generateSchedule();

        // Assert
        $this->assertCount(4, $persistedGames, 'Should persist 4 games');

        $expectedPairs = [
            [1, $teamA, $teamB],
            [1, $teamC, $teamD],
            [2, $teamA, $teamC],
            [2, $teamB, $teamD],
        ];

        foreach ($persistedGames as $index => $game) {
            $this->assertInstanceOf(Game::class, $game);
            $this->assertSame($expectedPairs[$index][0], $game->getWeek(), "Game #$index should be in week {$expectedPairs[$index][0]}");
            $this->assertSame($expectedPairs[$index][1], $game->getHomeTeam(), "Game #$index has incorrect home team");
            $this->assertSame($expectedPairs[$index][2], $game->getAwayTeam(), "Game #$index has incorrect away team");
        }
    }
}
