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
        $hours = new HourCollection();

        for ($i = 9; $i < 20; $i++) {
            $hours::add(new Hour($i, false));
        }

        return $hours;
    }

    public function reserve(int $hour): HourCollection
    {
        $this->hours[$hour] = new Hour($hour, true);

        return new self($this->hours);
    }

    private function add(Hour $hour): void
    {
        $this->hours[$hour->value()] = $hour;
    }


    public function toArray(): array
    {
        return array_map(fn (Hour $hour) => $hour->toArray(), $this->hours);
    }
}
