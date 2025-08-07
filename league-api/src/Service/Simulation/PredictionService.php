<?php

declare(strict_types=1);

namespace App\Service\Simulation;

use App\Dto\Team\TeamWinProbabilityDto;
use App\Entity\Game;
use App\Entity\Team;
use App\Entity\TeamStanding;
use App\Factory\Team\TeamWinProbabilityDtoFactory;
use App\Repository\GameRepository;
use App\Repository\TeamStandingRepository;
use App\Simulation\MatchOutcomeStrategyInterface;
use App\Simulation\MatchSimulatorInterface;

readonly class PredictionService
{
    public function __construct(
        private GameRepository                $gameRepository,
        private TeamStandingRepository        $standingRepository,
        private MatchSimulatorInterface       $simulator,
        private MatchOutcomeStrategyInterface $outcomeStrategy,
        private TeamWinProbabilityDtoFactory  $dtoFactory,
    ) {}

    /**
     * @param int $simulations
     * @return array<int, TeamWinProbabilityDto>
     */
    public function calculateChampionProbabilities(int $simulations = 1000): array
    {
        $teamNames = array_map(
            fn(TeamStanding $s) => $s->getTeam()->getName(),
            $this->standingRepository->findAll()
        );
        $winCounts = array_fill_keys($teamNames, 0);

        for ($i = 0; $i < $simulations; $i++) {
            $winner = $this->runSingleSimulation();
            $winCounts[$winner->getName()]++;
        }

        return array_map(
            fn(string $teamName) => $this->dtoFactory->create(
                teamName: $teamName,
                finalPoints: 0,
                winProbability: round($winCounts[$teamName] / $simulations, 4)
            ),
            array_keys($winCounts)
        );
    }

    protected function runSingleSimulation(): Team
    {
        $standings = $this->cloneStandings($this->standingRepository->findAll());
        $games = $this->cloneGames($this->gameRepository->findUnplayedGames());

        foreach ($games as $game) {
            $this->simulator->simulate($game);

            $home = $game->getHomeTeam();
            $away = $game->getAwayTeam();

            $homeStanding = $standings[$home->getId()];
            $awayStanding = $standings[$away->getId()];

            $this->applyGameResult($game, $homeStanding, $awayStanding);
        }

        [$winner] = $this->sortStandings(array_values($standings)) + [null];

        if (!$winner instanceof TeamStanding) {
            throw new \RuntimeException('No standings available to determine winner.');
        }

        return $winner->getTeam();
    }


    protected function applyGameResult(Game $game, TeamStanding $home, TeamStanding $away): void
    {
        $home
            ->setPlayed($home->getPlayed() + 1)
            ->setGoalsFor($home->getGoalsFor() + $game->getHomeGoals())
            ->setGoalsAgainst($home->getGoalsAgainst() + $game->getAwayGoals());

        $away
            ->setPlayed($away->getPlayed() + 1)
            ->setGoalsFor($away->getGoalsFor() + $game->getAwayGoals())
            ->setGoalsAgainst($away->getGoalsAgainst() + $game->getHomeGoals());

        $this->outcomeStrategy->apply($game, $home, $away);
    }

    /**
     * @param TeamStanding[] $originals
     * @return TeamStanding[] (assoc by team ID)
     */
    private function cloneStandings(array $originals): array
    {
        $clones = [];
        foreach ($originals as $standing) {
            $clone = new TeamStanding();
            $clone->setTeam($standing->getTeam());
            $clone->setPlayed($standing->getPlayed());
            $clone->setWins($standing->getWins());
            $clone->setDraws($standing->getDraws());
            $clone->setLosses($standing->getLosses());
            $clone->setGoalsFor($standing->getGoalsFor());
            $clone->setGoalsAgainst($standing->getGoalsAgainst());
            $clone->setPoints($standing->getPoints());

            $clones[$standing->getTeam()->getId()] = $clone;
        }
        return $clones;
    }

    /**
     * @param Game[] $games
     * @return Game[]
     */
    private function cloneGames(array $games): array
    {
        return array_map(function (Game $g) {
            $clone = new Game();
            $clone->setWeek($g->getWeek());
            $clone->setHomeTeam($g->getHomeTeam());
            $clone->setAwayTeam($g->getAwayTeam());
            return $clone;
        }, $games);
    }

    /**
     * @param TeamStanding[] $standings
     * @return TeamStanding[]
     */
    private function sortStandings(array $standings): array
    {
        usort($standings, fn(TeamStanding $a, TeamStanding $b) => [
                $b->getPoints(),
                $b->getGoalDifference(),
                $b->getGoalsFor()
            ] <=> [
                $a->getPoints(),
                $a->getGoalDifference(),
                $a->getGoalsFor()
            ]);
        return $standings;
    }
}
