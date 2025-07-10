<?php

namespace App\Tests\Service;

use App\Repository\GameRepository;
use App\Service\SeasonSimulator;
use App\Service\WeekSimulator;
use PHPUnit\Framework\TestCase;

class SeasonSimulatorTest extends TestCase
{
    public function testSimulateCallsWeekSimulatorForEachWeek(): void
    {
        $weeks = [
            ['week' => 1],
            ['week' => 2],
            ['week' => 3],
        ];

        $gameRepo = $this->createMock(GameRepository::class);
        $gameRepo->expects($this->once())
            ->method('findAllWeeks')
            ->willReturn($weeks);

        $calledWeeks = [];

        $weekSimulator = $this->createMock(WeekSimulator::class);
        $weekSimulator->expects($this->exactly(3))
            ->method('simulate')
            ->willReturnCallback(function (int $week) use (&$calledWeeks) {
                $calledWeeks[] = $week;
            });

        $service = new SeasonSimulator($gameRepo, $weekSimulator);
        $service->simulate();

        $this->assertSame([1, 2, 3], $calledWeeks);
    }

    public function testSimulateSkipsWhenNoWeeksFound(): void
    {
        $gameRepo = $this->createMock(GameRepository::class);
        $gameRepo->expects($this->once())
            ->method('findAllWeeks')
            ->willReturn([]); // No weeks

        $weekSimulator = $this->createMock(WeekSimulator::class);
        $weekSimulator->expects($this->never())
            ->method('simulate');

        $service = new SeasonSimulator($gameRepo, $weekSimulator);
        $service->simulate();

        $this->assertTrue(true); // test passes as long as simulate() was not called
    }
}
