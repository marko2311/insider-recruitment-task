<?php

declare(strict_types=1);

namespace App\Service\Schedule;

use App\Dto\Match\MatchPairDto;
use Doctrine\Common\Collections\Collection;

readonly class MatchPairGenerator implements MatchPairGeneratorInterface
{
    public function generate(Collection $teams): array
    {
        $teamList = $this->prepareTeamList($teams);
        $firstLeg = $this->generateRoundRobin($teamList);
        $secondLeg = $this->generateReturnLeg($firstLeg);

        return array_merge($firstLeg, $secondLeg);
    }

    private function prepareTeamList(Collection $teams): array
    {
        $list = array_values($teams->toArray());

        if (count($list) % 2 !== 0) {
            $list[] = null;
        }

        return $list;
    }

    private function generateRoundRobin(array $teamList): array
    {
        $teamCount = count($teamList);
        $rounds = $teamCount - 1;
        $half = $teamCount / 2;

        $schedule = [];

        for ($round = 0; $round < $rounds; $round++) {
            $week = [];

            for ($i = 0; $i < $half; $i++) {
                $home = $teamList[$i];
                $away = $teamList[$teamCount - 1 - $i];

                if ($home !== null && $away !== null) {
                    $week[] = new MatchPairDto($home, $away);
                }
            }

            $schedule[] = $week;
            $teamList = $this->rotateTeams($teamList);
        }

        return $schedule;
    }

    private function generateReturnLeg(array $firstLeg): array
    {
        $returnLeg = [];

        foreach ($firstLeg as $week) {
            $rematches = [];

            foreach ($week as $match) {
                if(is_null($match)){
                    continue;
                }
                $rematches[] = new MatchPairDto(
                    $match->getAwayTeam(),
                    $match->getHomeTeam()
                );
            }

            $returnLeg[] = $rematches;
        }

        return $returnLeg;
    }

    private function rotateTeams(array $teams): array
    {
        return array_merge(
            [$teams[0]],
            [end($teams)],
            array_slice($teams, 1, -1)
        );
    }
}
