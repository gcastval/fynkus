<?php

declare(strict_types=1);

namespace App\Core\CommonArea\Domain;

use App\Shared\Domain\DomainError;

class HourOutOfRangeError extends DomainError
{
    public function __construct()
    {
        parent::__construct('Hour is out of range', 400);
    }
}
