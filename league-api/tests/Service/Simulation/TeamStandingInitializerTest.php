<?php

declare(strict_types=1);

namespace App\Tests\Service\Simulation;

use App\Entity\Team;
use App\Entity\TeamStanding;
use App\Repository\TeamRepository;
use App\Repository\TeamStandingRepository;
use App\Service\Simulation\TeamStandingInitializer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class TeamStandingInitializerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testInitializePersistsStandingsForTeamsWithoutStandings(): void
    {
        // Arrange
        $team1 = $this->createMock(Team::class);
        $team2 = $this->createMock(Team::class);

        $teamRepo = $this->createMock(TeamRepository::class);
        $teamRepo->method('findAll')->willReturn([$team1, $team2]);

        $standingRepo = $this->createMock(TeamStandingRepository::class);
        $standingRepo->method('findOneBy')->willReturnCallback(
            fn(array $criteria) => $criteria['team'] === $team2 ? new TeamStanding() : null
        );

        $persistedEntities = [];

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('persist')->willReturnCallback(function ($entity) use (&$persistedEntities) {
            $persistedEntities[] = $entity;
        });
        $em->expects($this->once())->method('flush');

        $initializer = new TeamStandingInitializer($teamRepo, $standingRepo, $em);

        // Act
        $initializer->initialize();

        // Assert
        $this->assertCount(1, $persistedEntities, 'Only one new TeamStanding should be persisted');
        $this->assertInstanceOf(TeamStanding::class, $persistedEntities[0]);
        $this->assertSame($team1, $persistedEntities[0]->getTeam());
    }

    /**
     * @throws Exception
     */
    public function testInitializeSkipsAllIfAllTeamsHaveStandings(): void
    {
        // Arrange
        $teamA = $this->createMock(Team::class);
        $teamB = $this->createMock(Team::class);

        $teamRepo = $this->createMock(TeamRepository::class);
        $teamRepo->method('findAll')->willReturn([$teamA, $teamB]);

        $standingRepo = $this->createMock(TeamStandingRepository::class);
        $standingRepo->method('findOneBy')->willReturn(new TeamStanding());

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('persist');
        $em->expects($this->once())->method('flush');

        $initializer = new TeamStandingInitializer($teamRepo, $standingRepo, $em);

        // Act
        $initializer->initialize();
    }

    /**
     * @throws Exception
     */
    public function testInitializeFlushesEvenIfNoTeamsFound(): void
    {
        // Arrange
        $teamRepo = $this->createMock(TeamRepository::class);
        $teamRepo->method('findAll')->willReturn([]);

        $standingRepo = $this->createMock(TeamStandingRepository::class);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('persist');
        $em->expects($this->once())->method('flush');

        $initializer = new TeamStandingInitializer($teamRepo, $standingRepo, $em);

        // Act
        $initializer->initialize();
    }
}
