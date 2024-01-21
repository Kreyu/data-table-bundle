<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ProductAttribute
{
    public function __construct(
        #[ORM\Id, ORM\ManyToOne]
        private Product $product,
        #[ORM\Column]
        private readonly string $name,
    ) {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
