<?php

declare(strict_types=1);

namespace App\Core\CommonArea\Domain;

use App\Shared\Domain\DomainError;

class HourNotAvailableError extends DomainError
{
    public function __construct()
    {
        parent::__construct('Hour is not available', 400);
    }
}
