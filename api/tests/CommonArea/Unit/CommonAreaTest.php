<?php

declare(strict_types=1);

namespace App\Tests\CommonArea\Unit;

use App\Core\CommonArea\Application\CommonAreaReservationsDTO;
use App\Core\CommonArea\Application\CommonAreaReservationsFinder;
use App\Core\CommonArea\Application\CommonAreaReservator;
use App\Core\CommonArea\Domain\Area;
use App\Core\CommonArea\Domain\CommonArea;
use App\Core\CommonArea\Domain\CommonAreaRepository;
use App\Core\CommonArea\Domain\Hour;
use App\Core\CommonArea\Domain\HourCollection;
use App\Core\CommonArea\Domain\HourNotAvailableError;
use App\Core\CommonArea\Domain\HourOutOfRangeError;
use App\Tests\Shared\AbstractTestCase;

class CommonAreaTest extends AbstractTestCase
{

    public function testAllHoursAreAvailableWhenCreated(): void
    {
        $hourCollection = HourCollection::create();

        $this->assertCount(13, $hourCollection->getIterator());

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

        $this->expectException(HourOutOfRangeError::class);
        $reservator->handle(Area::GYM, new \DateTimeImmutable('2023-01-01'), 8);

        $this->expectException(HourOutOfRangeError::class);
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

        $this->expectException(HourNotAvailableError::class);


        $reservator->handle(Area::GYM, new \DateTimeImmutable('2023-01-01'), 10);
    }

    public function testGetCommonAreaSchedule(): void
    {
        $repository = $this->createMock(CommonAreaRepository::class);

        $commonArea = CommonArea::create(Area::GYM, new \DateTimeImmutable('2023-01-01'));
        $commonArea->reserve(10);

        $repository->expects($this->once())
            ->method('findByAreaAndDate')
            ->willReturn($commonArea);

        $finder = new CommonAreaReservationsFinder($repository);

        $schedule = $finder->handle(Area::GYM, new \DateTimeImmutable('2023-01-01'));

        $this->assertInstanceOf(CommonAreaReservationsDTO::class, $schedule);
        $this->assertCount(13, $schedule->hours);
    }
}
