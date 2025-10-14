<?php

namespace App\Tests\HealthCheck;

use App\Tests\Shared\AbstractTestCase;

class HealthCheckTest extends AbstractTestCase
{
    public function testHealthCheck(): void
    {
        $this->client->request('GET', '/api/v1/health-check');

        $this->assertResponseIsSuccessful();
    }
}
