<?php

declare(strict_types=1);

namespace App\Tests\Service\Simulation;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\TeamStanding;
use App\Repository\TeamStandingRepository;
use App\Service\Simulation\StandingUpdater;
use App\Simulation\MatchOutcomeStrategyInterface;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class StandingUpdaterTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testUpdateAfterGameUpdatesStandingsAndPersists(): void
    {
        // Arrange
        $teamA = $this->createMock(Team::class);
        $teamB = $this->createMock(Team::class);

        $game = $this->createMock(Game::class);
        $game->method('getHomeGoals')->willReturn(2);
        $game->method('getAwayGoals')->willReturn(1);
        $game->method('getHomeTeam')->willReturn($teamA);
        $game->method('getAwayTeam')->willReturn($teamB);

        $homeStanding = (new TeamStanding())
            ->setPlayed(3)
            ->setGoalsFor(5)
            ->setGoalsAgainst(4)
            ->setTeam($teamA);

        $awayStanding = (new TeamStanding())
            ->setPlayed(3)
            ->setGoalsFor(4)
            ->setGoalsAgainst(3)
            ->setTeam($teamB);

        $repo = $this->createMock(TeamStandingRepository::class);
        $repo->method('findOneBy')->willReturnCallback(function (array $criteria) use ($teamA, $teamB, $homeStanding, $awayStanding) {
            return $criteria['team'] === $teamA ? $homeStanding : $awayStanding;
        });

        $strategy = $this->createMock(MatchOutcomeStrategyInterface::class);
        $strategy->expects($this->once())
            ->method('apply')
            ->with($game, $homeStanding, $awayStanding);

        $persisted = [];
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('persist')->willReturnCallback(function ($entity) use (&$persisted) {
            $persisted[] = $entity;
        });
        $em->expects($this->once())->method('flush');

        $updater = new StandingUpdater($repo, $strategy, $em);

        // Act
        $updater->updateAfterGame($game);

        // Assert
        $this->assertSame(4, $homeStanding->getPlayed());
        $this->assertSame(7, $homeStanding->getGoalsFor());
        $this->assertSame(5, $homeStanding->getGoalsAgainst());

        $this->assertSame(4, $awayStanding->getPlayed());
        $this->assertSame(5, $awayStanding->getGoalsFor());
        $this->assertSame(5, $awayStanding->getGoalsAgainst());

        $this->assertCount(2, $persisted);
        $this->assertContains($homeStanding, $persisted);
        $this->assertContains($awayStanding, $persisted);
    }

    /**
     * @throws Exception
     */
    public function testUpdateAfterGameThrowsWhenGoalsAreNull(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot update standings – game not finished.');

        $team = $this->createMock(Team::class);

        $game = $this->createMock(Game::class);
        $game->method('getHomeGoals')->willReturn(null);
        $game->method('getAwayGoals')->willReturn(1);
        $game->method('getHomeTeam')->willReturn($team);
        $game->method('getAwayTeam')->willReturn($team);

        $repo = $this->createMock(TeamStandingRepository::class);
        $strategy = $this->createMock(MatchOutcomeStrategyInterface::class);
        $em = $this->createMock(EntityManagerInterface::class);

        $updater = new StandingUpdater($repo, $strategy, $em);
        $updater->updateAfterGame($game);
    }

    /**
     * @throws Exception
     */
    public function testNewStandingIsCreatedIfNotFound(): void
    {
        // Arrange
        $team = $this->createMock(Team::class);

        $game = $this->createMock(Game::class);
        $game->method('getHomeGoals')->willReturn(3);
        $game->method('getAwayGoals')->willReturn(2);
        $game->method('getHomeTeam')->willReturn($team);
        $game->method('getAwayTeam')->willReturn($team);

        $repo = $this->createMock(TeamStandingRepository::class);
        $repo->method('findOneBy')->willReturn(null);

        $strategy = $this->createMock(MatchOutcomeStrategyInterface::class);
        $strategy->expects($this->once())->method('apply');

        $persisted = [];
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('persist')->willReturnCallback(function ($entity) use (&$persisted) {
            $persisted[] = $entity;
        });
        $em->expects($this->once())->method('flush');

        $updater = new StandingUpdater($repo, $strategy, $em);

        // Act
        $updater->updateAfterGame($game);

        // Assert
        $this->assertCount(2, $persisted);
        foreach ($persisted as $standing) {
            $this->assertInstanceOf(TeamStanding::class, $standing);
            $this->assertSame($team, $standing->getTeam());
        }
    }
}
