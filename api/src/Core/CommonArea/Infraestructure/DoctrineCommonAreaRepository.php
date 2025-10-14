<?php

declare(strict_types=1);

namespace App\Core\CommonArea\Infraestructure;

use App\Core\CommonArea\Domain\Area;
use App\Core\CommonArea\Domain\CommonArea;
use App\Core\CommonArea\Domain\CommonAreaRepository;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCommonAreaRepository implements CommonAreaRepository
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function findByAreaAndDate(Area $area, \DateTimeInterface $date): ?CommonArea
    {
        return $this->entityManager->getRepository(CommonArea::class)
            ->findOneBy(['area' => $area->value, 'date' => $date]);
    }

    public function save(CommonArea $commonArea): void
    {
        $this->entityManager->persist($commonArea);
        $this->entityManager->flush();
    }
}
