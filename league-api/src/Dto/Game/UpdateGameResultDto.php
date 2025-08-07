<?php

declare(strict_types=1);

namespace App\Dto\Game;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdateGameResultDto
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Type('integer')]
        #[Assert\GreaterThanOrEqual(0)]
        private ?int $homeGoals = null,
        #[Assert\NotNull]
        #[Assert\Type('integer')]
        #[Assert\GreaterThanOrEqual(0)]
        private ?int $awayGoals = null
    ){}

    public function getHomeGoals(): ?int
    {
        return $this->homeGoals;
    }

    public function getAwayGoals(): ?int
    {
        return $this->awayGoals;
    }
}
