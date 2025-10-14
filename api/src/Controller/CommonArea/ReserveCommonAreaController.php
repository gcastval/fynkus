<?php

declare(strict_types=1);

namespace App\Controller\CommonArea;

use App\Core\CommonArea\Aplication\CommonAreaReservator;
use App\Core\CommonArea\Aplication\ReserveCommonAreaDTO;
use App\Core\CommonArea\Domain\Area;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ReserveCommonAreaController extends AbstractController
{
    public function __construct(
        private readonly CommonAreaReservator $commonAreaReservator
    )
    {
    }

    #[Route(path: '/api/v1/common-area/reserve', name: 'reserve-common-area', methods: ['POST'])]
    public function __invoke(ReserveCommonAreaDTO $request): JsonResponse
    {
        $this->commonAreaReservator->handle(
            Area::from($request->area),
            new \DateTimeImmutable($request->date),
            $request->hour
        );

        return new JsonResponse([
            'status' => 'fail',
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
}
