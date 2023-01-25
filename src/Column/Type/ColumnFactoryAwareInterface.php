<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;

interface ColumnFactoryAwareInterface
{
    public function setColumnFactory(ColumnFactoryInterface $columnFactory): void;
}
