<?php

declare(strict_types=1);

namespace App\Service\Schedule;

use App\Dto\Match\MatchPairDto;
use Doctrine\Common\Collections\Collection;

/**
 * @return MatchPairDto[]
 */
interface MatchPairGeneratorInterface
{
    public function generate(Collection $teams): array;
}
