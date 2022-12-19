<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Factory\ColumnFactoryInterface;

interface ColumnFactoryAwareTypeInterface
{
    public function setColumnFactory(ColumnFactoryInterface $columnFactory): void;
}
