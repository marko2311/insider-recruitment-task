<?php

namespace App\DataFixtures;

use App\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $teams = [
            ['name' => 'Manchester City', 'strength' => 95],
            ['name' => 'Liverpool', 'strength' => 90],
            ['name' => 'Arsenal', 'strength' => 88],
            ['name' => 'Manchester United', 'strength' => 85],
        ];

        foreach ($teams as $teamData) {
            $team = new Team();
            $team->setName($teamData['name']);
            $team->setStrength($teamData['strength']);
            $manager->persist($team);
        }

        $manager->flush();
    }
}
