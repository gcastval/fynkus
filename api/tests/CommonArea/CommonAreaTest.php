<?php

declare(strict_types=1);

namespace App\Tests\CommonArea;

use App\Core\CommonArea\Aplication\CommonAreaReservator;
use App\Core\CommonArea\Domain\Area;
use App\Core\CommonArea\Domain\CommonArea;
use App\Core\CommonArea\Domain\CommonAreaRepository;
use App\Core\CommonArea\Domain\HourCollection;
use App\Tests\Shared\AbstractTestCase;

class CommonAreaTest extends AbstractTestCase
{

    public function testAllHoursAreAvailableWhenCreated(): void
    {
        $hourCollection = HourCollection::create();

        $this->assertCount(13, $hourCollection->toArray());

        foreach ($hourCollection->getIterator() as $hour) {
            $this->assertTrue(!$hour->isReserved());
        }

    }

    public function testFailWhenReservingAnHourOutOrRange(): void
    {
        $repository = $this->createMock(CommonAreaRepository::class);

        $commonArea = CommonArea::create(Area::GYM, new \DateTimeImmutable('2023-01-01'));

        $repository->expects($this->once())
            ->method('findByAreaAndDate')
            ->willReturn($commonArea);

        $reservator = new CommonAreaReservator($repository);

        $this->expectException(\RuntimeException::class);
        $reservator->handle(Area::GYM, new \DateTimeImmutable('2023-01-01'), 8);

        $this->expectException(\RuntimeException::class);
        $reservator->handle(Area::GYM, new \DateTimeImmutable('2023-01-01'), 22);

    }

    public function testFailWhenReservingHourThatIsNotAvailable(): void
    {
        $repository = $this->createMock(CommonAreaRepository::class);

        $commonArea = CommonArea::create(Area::GYM, new \DateTimeImmutable('2023-01-01'));
        $commonArea->reserve(10);

        $repository->expects($this->once())
            ->method('findByAreaAndDate')
            ->willReturn($commonArea);

        $reservator = new CommonAreaReservator($repository);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Hour is not available');

        $reservator->handle(Area::GYM, new \DateTimeImmutable('2023-01-01'), 10);
    }


}
