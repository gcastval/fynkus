<?php

declare(strict_types=1);

namespace App\Core\CommonArea\Application;

class GetCommonAreaReservationsRequest
{
    public function __construct(
        public readonly string $area,
        public readonly string $date,
    ) {
    }
}
