<?php

declare(strict_types=1);

namespace App\Tests\CommonArea\E2E;

use App\Tests\Shared\AbstractTestCase;

class ReserveCommonAreaTest extends AbstractTestCase
{
    public function testReserveCommonArea(): void
    {
        $this->client->request('POST', '/api/v1/common-area/reserve', [
            'area' => 'gym',
            'date' => '2023-01-01',
            'hour' => 10,
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testFailWhenReservingAreaThatIsNotAvailable(): void
    {
        $this->client->request('POST', '/api/v1/common-area/reserve', [
            'area' => 'gym',
            'date' => '2023-01-01',
            'hour' => 10,
        ]);

        $this->assertResponseIsSuccessful();

        $this->client->request('POST', '/api/v1/common-area/reserve', [
            'area' => 'gym',
            'date' => '2023-01-01',
            'hour' => 10,
        ]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testGetCommonAreaSchedule(): void
    {
        $this->client->request('GET', '/api/v1/common-area/schedule/gym/2023-01-01');

        $this->assertResponseIsSuccessful();
    }
}
