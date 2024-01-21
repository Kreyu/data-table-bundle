<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Category
{
    public function __construct(
        #[ORM\Id, ORM\Column]
        private readonly int $id,
        #[ORM\Column]
        private readonly string $name,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
