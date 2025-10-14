<?php

declare(strict_types=1);

namespace App\Core\CommonArea\Domain;

class Hour
{

    public function __construct(
        private readonly int $hour,
        private readonly bool $reserved,
    ) {
    }

    public function value(): int
    {
        return $this->hour;
    }

    public function isReserved(): bool
    {
        return $this->reserved;
    }

    public function toArray(): array
    {
        return [
            'hour' => $this->hour,
            'reserved' => $this->reserved,
        ];
    }

    public function fromArray(): array
    {
        return $this->toArray();
    }
}
