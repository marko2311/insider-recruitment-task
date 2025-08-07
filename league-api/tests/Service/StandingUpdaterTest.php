<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\TeamStanding;
use App\Repository\TeamStandingRepository;
use App\Service\StandingUpdater;
use App\Simulation\MatchOutcomeStrategyInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class StandingUpdaterTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testUpdateAfterGameWithExistingStandings(): void
    {
        $homeTeam = (new Team())->setName('Home FC');
        $awayTeam = (new Team())->setName('Away FC');

        $game = (new Game())
            ->setHomeTeam($homeTeam)
            ->setAwayTeam($awayTeam)
            ->setHomeGoals(2)
            ->setAwayGoals(1);

        $homeStanding = (new TeamStanding())
            ->setTeam($homeTeam)
            ->setPlayed(3)
            ->setGoalsFor(4)
            ->setGoalsAgainst(3);

        $awayStanding = (new TeamStanding())
            ->setTeam($awayTeam)
            ->setPlayed(3)
            ->setGoalsFor(5)
            ->setGoalsAgainst(6);

        $repo = $this->createMock(TeamStandingRepository::class);
        $repo->method('findOneBy')->willReturnCallback(function ($criteria) use ($homeTeam, $homeStanding, $awayStanding) {
            return $criteria['team'] === $homeTeam ? $homeStanding : $awayStanding;
        });

        $strategy = $this->createMock(MatchOutcomeStrategyInterface::class);
        $strategy->expects($this->once())
            ->method('apply')
            ->with($game, $homeStanding, $awayStanding);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->exactly(2))
            ->method('persist');

        $service = new StandingUpdater($repo, $strategy, $em);
        $service->updateAfterGame($game);

        $this->assertEquals(4, $homeStanding->getPlayed());
        $this->assertEquals(6, $homeStanding->getGoalsFor());
        $this->assertEquals(4, $homeStanding->getGoalsAgainst());

        $this->assertEquals(4, $awayStanding->getPlayed());
        $this->assertEquals(6, $awayStanding->getGoalsFor());
        $this->assertEquals(8, $awayStanding->getGoalsAgainst());
    }

    /**
     * @throws Exception
     */
    public function testUpdateAfterGameWithMissingStandings(): void
    {
        $homeTeam = (new Team())->setName('New Home');
        $awayTeam = (new Team())->setName('New Away');

        $game = (new Game())
            ->setHomeTeam($homeTeam)
            ->setAwayTeam($awayTeam)
            ->setHomeGoals(0)
            ->setAwayGoals(0);

        $repo = $this->createMock(TeamStandingRepository::class);
        $repo->method('findOneBy')->willReturn(null); // both standings missing

        $strategy = $this->createMock(MatchOutcomeStrategyInterface::class);
        $strategy->expects($this->once())
            ->method('apply')
            ->with(
                $game,
                $this->callback(fn($ts) => $ts instanceof TeamStanding && $ts->getTeam() === $homeTeam),
                $this->callback(fn($ts) => $ts instanceof TeamStanding && $ts->getTeam() === $awayTeam)
            );

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->exactly(2))->method('persist');

        $service = new StandingUpdater($repo, $strategy, $em);
        $service->updateAfterGame($game);
    }
}
