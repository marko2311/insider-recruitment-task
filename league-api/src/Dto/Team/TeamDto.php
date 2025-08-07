<?php

declare(strict_types=1);

namespace App\Dto\Team;

readonly class TeamDto
{
    public function __construct(
        private int $id,
        private string $name,
        private int $strength
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStrength(): int
    {
        return $this->strength;
    }
}
