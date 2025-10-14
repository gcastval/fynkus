<?php

declare(strict_types=1);


namespace App\Core\CommonArea\Application;

class ReserveCommonAreaRequest
{
    public function __construct(
        public readonly string $area,
        public readonly string $date,
        public readonly int $hour,
    ) {
    }
}
