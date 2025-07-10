<?php

namespace App\Tests\Service;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Service\Generator\GameResultGeneratorInterface;
use App\Service\StandingUpdater;
use App\Service\WeekSimulator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class WeekSimulatorTest extends TestCase
{
    public function testSimulatesUnplayedGamesAndUpdatesStandings(): void
    {
        $week = 2;

        $game1 = new Game();
        $game1->setWeek($week);
        $game1->setHomeGoals(null)->setAwayGoals(null);

        $game2 = new Game();
        $game2->setWeek($week);
        $game2->setHomeGoals(null)->setAwayGoals(null);

        $game3 = new Game();
        $game3->setWeek($week);
        $game3->setHomeGoals(1)->setAwayGoals(1);

        $repo = $this->createMock(GameRepository::class);
        $repo->method('findBy')
            ->with(['week' => $week])
            ->willReturn([$game1, $game2, $game3]);

        $resultGenerator = $this->createMock(GameResultGeneratorInterface::class);
        $resultGenerator->expects($this->exactly(2))
            ->method('generate')
            ->willReturnOnConsecutiveCalls([2, 1], [1, 3]);

        $updater = $this->createMock(StandingUpdater::class);
        $updater->expects($this->exactly(2))
            ->method('updateAfterGame')
            ->with($this->callback(function ($game) use ($game1, $game2) {
                return $game === $game1 || $game === $game2;
            }));

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->exactly(2))
            ->method('persist')
            ->with($this->callback(function ($game) use ($game1, $game2) {
                return $game === $game1 || $game === $game2;
            }));
        $em->expects($this->once())->method('flush');
        $em->expects($this->once())->method('clear');

        $simulator = new WeekSimulator($repo, $resultGenerator, $updater, $em);
        $simulator->simulate($week);

        $this->assertEquals(2, $game1->getHomeGoals());
        $this->assertEquals(1, $game1->getAwayGoals());
        $this->assertEquals(1, $game2->getHomeGoals());
        $this->assertEquals(3, $game2->getAwayGoals());
        $this->assertEquals(1, $game3->getHomeGoals());
        $this->assertEquals(1, $game3->getAwayGoals());
    }
}
