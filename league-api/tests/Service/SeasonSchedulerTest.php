<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Game;
use App\Entity\Team;
use App\Dto\MatchPairDto;
use App\Repository\TeamRepository;
use App\Schedule\MatchPairGeneratorInterface;
use App\Service\SeasonScheduler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SeasonSchedulerTest extends TestCase
{
    public function testGenerateScheduleCreatesGamesWithCorrectTeamsAndWeeks(): void
    {
        $teamA = $this->createMock(Team::class);
        $teamB = $this->createMock(Team::class);
        $teamC = $this->createMock(Team::class);

        $teams = [$teamA, $teamB, $teamC];

        $teamRepo = $this->createMock(TeamRepository::class);
        $teamRepo->expects($this->once())
            ->method('findAll')
            ->willReturn($teams);

        $pair1 = $this->createConfiguredMock(MatchPairDto::class, [
            'getHomeTeam' => $teamA,
            'getAwayTeam' => $teamB,
        ]);
        $pair2 = $this->createConfiguredMock(MatchPairDto::class, [
            'getHomeTeam' => $teamC,
            'getAwayTeam' => $teamA,
        ]);

        $pairGenerator = $this->createMock(MatchPairGeneratorInterface::class);
        $pairGenerator->expects($this->once())
            ->method('generate')
            ->willReturn([$pair1, $pair2]);

        $persistedGames = [];
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->exactly(2))
            ->method('persist')
            ->willReturnCallback(function (Game $game) use (&$persistedGames) {
                $persistedGames[] = $game;
            });
        $em->expects($this->once())
            ->method('flush');

        $scheduler = new SeasonScheduler($teamRepo, $pairGenerator, $em);
        $scheduler->generateSchedule();

        // assertions
        $this->assertCount(2, $persistedGames);
        $this->assertEquals($teamA, $persistedGames[0]->getHomeTeam());
        $this->assertEquals($teamB, $persistedGames[0]->getAwayTeam());
        $this->assertSame(1, $persistedGames[0]->getWeek());

        $this->assertEquals($teamC, $persistedGames[1]->getHomeTeam());
        $this->assertEquals($teamA, $persistedGames[1]->getAwayTeam());
        $this->assertSame(2, $persistedGames[1]->getWeek());
    }

    public function testGenerateScheduleDoesNothingWhenNoTeams(): void
    {
        $teamRepo = $this->createMock(TeamRepository::class);
        $teamRepo->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $pairGenerator = $this->createMock(MatchPairGeneratorInterface::class);
        $pairGenerator->expects($this->once())
            ->method('generate')
            ->willReturn([]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('persist');
        $em->expects($this->once())->method('flush');

        $scheduler = new SeasonScheduler($teamRepo, $pairGenerator, $em);
        $scheduler->generateSchedule();

        $this->assertTrue(true); // Ensure no exception is thrown
    }
}
