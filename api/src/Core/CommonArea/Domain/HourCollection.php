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
            $hours["$i:hour"] = [
                'hour' => $i,
                'reserved' => false,
            ];
        }
        return new self($hours);
    }

    public function reserve(int $hour): HourCollection
    {
        if ($hour < 9 || $hour > 21) {
            throw new HourOutOfRangeError('Hour is out of range');
        }

        if (!$this->isHourAvailable($hour)) {
            throw new HourNotAvailableError('Hour is not available');
        }

        $this->hours["$hour:hour"] = [
            'hour' => $hour,
            'reserved' => true,
        ];

        return new self($this->hours);
    }

    public function isHourAvailable(int $hour): bool
    {
        if (!isset($this->hours["$hour:hour"])) {
            return false;
        }

        return !$this->hours["$hour:hour"]['reserved'];
    }

    /**
     * @return Hour[]
     */
    public function getIterator(): array
    {
        return array_map(fn ($hour) => new Hour($hour['hour'], $hour['reserved']), $this->hours);
    }
}
