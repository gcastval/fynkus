<?php

declare(strict_types=1);


namespace App\Core\CommonArea\Aplication;

class ReserveCommonAreaDTO
{
    public function __construct(
        public readonly string $area,
        public readonly string $date,
        public readonly int $hour,
    ) {
    }
}
