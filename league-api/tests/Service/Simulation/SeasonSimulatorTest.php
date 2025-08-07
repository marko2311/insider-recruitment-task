<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\Simulation;

use App\Service\Simulation\SeasonSimulator;
use App\Service\Simulation\WeekSimulator;
use App\Repository\GameRepository;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class SeasonSimulatorTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSimulateDelegatesToWeekSimulatorForEachWeek(): void
    {
        // Arrange
        $gameRepository = $this->createMock(GameRepository::class);
        $gameRepository->method('findAllWeeks')->willReturn([
            ['week' => 1],
            ['week' => 2],
            ['week' => 3],
        ]);

        $calledWeeks = [];

        $weekSimulator = $this->createMock(WeekSimulator::class);
        $weekSimulator->method('simulate')
            ->willReturnCallback(function (int $week) use (&$calledWeeks) {
                $calledWeeks[] = $week;
            });

        $simulator = new SeasonSimulator($gameRepository, $weekSimulator);

        // Act
        $simulator->simulate();

        // Assert
        $this->assertSame([1, 2, 3], $calledWeeks);
    }
}
