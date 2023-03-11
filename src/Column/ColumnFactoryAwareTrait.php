<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Symfony\Contracts\Service\Attribute\Required;

/**
 * @see ColumnFactoryAwareInterface
 */
trait ColumnFactoryAwareTrait
{
    private ?ColumnFactoryInterface $columnFactory = null;

    #[Required]
    public function setColumnFactory(ColumnFactoryInterface $columnFactory): void
    {
        $this->columnFactory = $columnFactory;
    }
}
