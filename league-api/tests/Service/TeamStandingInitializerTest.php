<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Team;
use App\Entity\TeamStanding;
use App\Repository\TeamRepository;
use App\Repository\TeamStandingRepository;
use App\Service\TeamStandingInitializer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TeamStandingInitializerTest extends TestCase
{
    public function testInitializesStandingsOnlyForTeamsWithoutExistingStanding(): void
    {
        // Given
        $teamA = (new Team())->setName('Team A');
        $teamB = (new Team())->setName('Team B');

        $teams = [$teamA, $teamB];

        $teamRepo = $this->createMock(TeamRepository::class);
        $teamRepo->method('findAll')->willReturn($teams);

        $standingRepo = $this->createMock(TeamStandingRepository::class);
        $standingRepo->method('findOneBy')
            ->willReturnCallback(fn(array $criteria) => $criteria['team'] === $teamA ? new TeamStanding() : null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())
            ->method('persist')
            ->with($this->callback(fn($standing) =>
                $standing instanceof TeamStanding &&
                $standing->getTeam() === $teamB
            ));
        $em->expects($this->once())->method('flush');

        // When
        $initializer = new TeamStandingInitializer($teamRepo, $standingRepo, $em);
        $initializer->initialize();
    }
}
