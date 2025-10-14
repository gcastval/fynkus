<?php

declare(strict_types=1);

namespace App\Core\CommonArea\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CommonArea
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[ORM\Column(type: "datetime", nullable: false)]
    private readonly \DateTimeInterface $date;

    #[ORM\Column(type: "string", nullable: false)]
    private readonly string $area;

    #[ORM\Embedded(class: HourCollection::class, columnPrefix: "day_")]
    private HourCollection $hours;

    private function __construct(
        CommonArea $area,
        \DateTimeInterface $date,
        HourCollection $hours,
    ) {
        $this->area = $area->value;
        $this->hours = $hours;
        $this->date = $date;
    }

    public static function create(CommonArea $area, \DateTimeInterface $date): self
    {
        return new self($area, $date, HourCollection::create());
    }

    public function reserve(int $hour): self
    {
        $this->hours = $this->hours->reserve($hour);

        return $this;
    }

}
