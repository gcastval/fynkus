<?php

declare(strict_types=1);

namespace App\Core\CommonArea\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class HourCollection
{
    #[ORM\Column(type: "json", nullable: false)]
    private array $hours;

    public function __construct(array $hours = [])
    {
        $this->hours = $hours;
    }

    public static function create(): self
    {
        $hours = [];

        for ($i = 9; $i < 22; $i++) {
            $hours[] = new Hour($i, false);
        }

        return new HourCollection($hours);
    }

    public function reserve(int $hour): HourCollection
    {
        if($hour < 9 || $hour > 21) {
            throw new \RuntimeException('Hour is out of range');
        }

        if(!$this->isHourAvailable($hour)) {
            throw new \RuntimeException('Hour is not available');
        }

        $this->hours[$hour] = new Hour($hour, true);

        return new self($this->hours);
    }

    public function isHourAvailable(int $hour): bool
    {
        return !$this->hours[$hour]->isReserved();
    }


    public function toArray(): array
    {
        return array_map(fn (Hour $hour) => $hour->toArray(), $this->hours);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->hours);
    }
}
