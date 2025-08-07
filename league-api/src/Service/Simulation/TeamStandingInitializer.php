<?php

declare(strict_types=1);

namespace App\Service\Simulation;

use App\Entity\Team;
use App\Entity\TeamStanding;
use App\Repository\TeamRepository;
use App\Repository\TeamStandingRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class TeamStandingInitializer
{
    public function __construct(
        private TeamRepository         $teamRepo,
        private TeamStandingRepository $standingRepo,
        private EntityManagerInterface $em,
    ) {}
    public function initialize(): void
    {
        /** @var Team[] $teams */
        $teams = $this->teamRepo->findAll();

        foreach ($teams as $team) {
            if ($this->isAlreadyInitialized($team)) {
                continue;
            }

            $standing = (new TeamStanding())->setTeam($team);
            $this->em->persist($standing);
        }

        $this->em->flush();
    }

    private function isAlreadyInitialized(Team $team): bool
    {
        return $this->standingRepo->findOneBy(['team' => $team]) !== null;
    }
}
