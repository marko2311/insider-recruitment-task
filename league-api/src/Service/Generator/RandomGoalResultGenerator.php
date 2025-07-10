<?php

namespace App\Service\Generator;

use Random\RandomException;

class RandomGoalResultGenerator implements GameResultGeneratorInterface
{
    /**
     * @throws RandomException
     */
    public function generate(): array
    {
        return [random_int(0, 5), random_int(0, 5)];
    }
}
