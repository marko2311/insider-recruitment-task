<?php

namespace App\Service\Generator;

interface GameResultGeneratorInterface
{
    /**
     * @return array{0: int, 1: int} // [homeGoals, awayGoals]
     */
    public function generate(): array;
}
