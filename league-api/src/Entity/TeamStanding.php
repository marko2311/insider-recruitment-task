<?php

namespace App\Entity;

use App\Repository\TeamStandingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamStandingRepository::class)]
class TeamStanding
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Team $team;

    #[ORM\Column]
    private int $played = 0;

    #[ORM\Column]
    private int $wins = 0;

    #[ORM\Column]
    private int $draws = 0;

    #[ORM\Column]
    private int $losses = 0;

    #[ORM\Column]
    private int $goalsFor = 0;

    #[ORM\Column]
    private int $goalsAgainst = 0;

    #[ORM\Column]
    private int $points = 0;

    public function getId(): ?int { return $this->id; }
    public function getTeam(): Team { return $this->team; }
    public function setTeam(Team $team): self { $this->team = $team; return $this; }

    public function getPlayed(): int { return $this->played; }
    public function setPlayed(int $played): self { $this->played = $played; return $this; }

    public function getWins(): int { return $this->wins; }
    public function setWins(int $wins): self { $this->wins = $wins; return $this; }

    public function getDraws(): int { return $this->draws; }
    public function setDraws(int $draws): self { $this->draws = $draws; return $this; }

    public function getLosses(): int { return $this->losses; }
    public function setLosses(int $losses): self { $this->losses = $losses; return $this; }

    public function getGoalsFor(): int { return $this->goalsFor; }
    public function setGoalsFor(int $gf): self { $this->goalsFor = $gf; return $this; }

    public function getGoalsAgainst(): int { return $this->goalsAgainst; }
    public function setGoalsAgainst(int $ga): self { $this->goalsAgainst = $ga; return $this; }

    public function getPoints(): int { return $this->points; }
    public function setPoints(int $points): self { $this->points = $points; return $this; }

    public function getGoalDifference(): int
    {
        return $this->goalsFor - $this->goalsAgainst;
    }
}
