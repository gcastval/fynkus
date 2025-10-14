<?php

declare(strict_types=1);

namespace App\Core\CommonArea\Domain;

interface CommonAreaRepository
{
    public function findByAreaAndDate(Area $area, \DateTimeInterface $date): ?CommonArea;

    public function save(CommonArea $commonArea): void;
}
