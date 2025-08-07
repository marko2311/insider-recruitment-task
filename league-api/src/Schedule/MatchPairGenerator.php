<?php

declare(strict_types=1);

namespace App\Schedule;

use App\Dto\MatchPairDto;
use Doctrine\Common\Collections\Collection;

class MatchPairGenerator implements MatchPairGeneratorInterface
{
    public function generate(Collection $teams): array
    {
        $pairs = [];
        $teamsList = $teams->toArray();

        foreach ($teamsList as $home) {
            foreach ($teamsList as $away) {
                if ($home === $away) continue;
                if (spl_object_id($home) > spl_object_id($away)) continue;

                $pairs[] = new MatchPairDto($home, $away);
                $pairs[] = new MatchPairDto($away, $home);
            }
        }

        return $pairs;
    }
}
