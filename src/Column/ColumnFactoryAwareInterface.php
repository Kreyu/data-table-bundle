<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

interface ColumnFactoryAwareInterface
{
    public function setColumnFactory(ColumnFactoryInterface $columnFactory): void;
}
