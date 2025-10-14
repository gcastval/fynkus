<?php

declare(strict_types=1);

namespace App\Core\CommonArea\Application;

use App\Core\CommonArea\Domain\CommonArea;

class CommonAreaReservationsDTO
{
    public function __construct(
        public readonly array $hours,
    )
    {
    }

    public static function fromCommonArea(CommonArea $commonArea): self
    {
        $hours = array_map(fn ($hour) => $hour->toArray(), $commonArea->getHours());

        return new self(
            $hours,
        );
    }
}
