<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\TeamStanding;
use App\Repository\TeamRepository;
use App\Repository\TeamStandingRepository;
use Doctrine\ORM\EntityManagerInterface;

class TeamStandingInitializer
{
    public function __construct(
        private readonly TeamRepository $teamRepo,
        private readonly TeamStandingRepository $standingRepo,
        private readonly EntityManagerInterface $em,
    ) {}

    public function initialize(): void
    {
        $teams = $this->teamRepo->findAll();

        foreach ($teams as $team) {
            $existing = $this->standingRepo->findOneBy(['team' => $team]);
            if ($existing !== null) {
                continue;
            }

            $standing = (new TeamStanding())->setTeam($team);
            $this->em->persist($standing);
        }

        $this->em->flush();
    }
}
