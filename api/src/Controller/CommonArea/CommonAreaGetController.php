<?php

declare(strict_types=1);

namespace App\Controller\CommonArea;

use App\Core\CommonArea\Application\CommonAreaReservationsFinder;
use App\Core\CommonArea\Application\GetCommonAreaReservationsRequest;
use App\Core\CommonArea\Domain\Area;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class CommonAreaGetController extends AbstractController
{
    public function __construct(
        private readonly CommonAreaReservationsFinder $finder
    )
    {
    }

    #[Route(path: '/api/v1/common-area/schedule/{area}/{date}', name: 'get-common-area-schedule', methods: ['GET'])]
    public function __invoke(string $area, string $date): JsonResponse
    {
        $schedule = $this->finder->handle(
            Area::from($area),
            new \DateTimeImmutable($date)
        );

        return new JsonResponse($schedule, JsonResponse::HTTP_OK);
    }
}
