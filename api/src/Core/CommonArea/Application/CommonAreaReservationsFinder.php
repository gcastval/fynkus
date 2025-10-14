<?php

namespace App\Core\CommonArea\Application;

use App\Core\CommonArea\Domain\Area;
use App\Core\CommonArea\Domain\CommonArea;
use App\Core\CommonArea\Domain\CommonAreaRepository;

class CommonAreaReservationsFinder
{
    public function __construct(
        private readonly CommonAreaRepository $commonAreaRepository
    )
    {
    }

    public function handle(Area $area, \DateTimeInterface $date): CommonAreaReservationsDTO
    {
        $commonArea = $this->commonAreaRepository->findByAreaAndDate($area, $date);

        if ($commonArea === null) {
            $commonArea = CommonArea::create($area, $date);
        }

        return CommonAreaReservationsDTO::fromCommonArea($commonArea);
    }
}
