<?php

declare(strict_types=1);

namespace App\Core\CommonArea\Aplication;

use App\Core\CommonArea\Domain\Area;
use App\Core\CommonArea\Domain\CommonArea;
use App\Core\CommonArea\Domain\CommonAreaRepository;
use App\Core\CommonArea\Domain\Hour;

class CommonAreaReservator
{
    public function __construct(
        private CommonAreaRepository $commonAreaRepository
    ) {
    }

    public function handle(Area $area, \DateTimeInterface $date, int $hour): void
    {
        $commonArea = $this->commonAreaRepository->findByAreaAndDate($area, $date);

        if ($commonArea === null) {
            $commonArea = CommonArea::create($area, $date);
        }

        $commonArea->reserve($hour);

        $this->commonAreaRepository->save($commonArea);
    }
}
