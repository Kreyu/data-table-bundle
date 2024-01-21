<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Car
{
    public function __construct(
        #[ORM\Id, ORM\Column]
        private readonly string $name,
        #[ORM\Id, ORM\Column]
        private readonly int $year,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getYear(): int
    {
        return $this->year;
    }
}
