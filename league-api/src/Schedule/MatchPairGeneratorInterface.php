<?php

declare(strict_types=1);

namespace App\Schedule;

use App\Dto\MatchPairDto;
use Doctrine\Common\Collections\Collection;

/**
 * @return MatchPairDto[]
 */
interface MatchPairGeneratorInterface
{
    public function generate(Collection $teams): array;
}
