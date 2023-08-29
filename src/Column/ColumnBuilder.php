<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

class ColumnBuilder extends ColumnConfigBuilder implements ColumnBuilderInterface
{
    public function getColumn(): ColumnInterface
    {
        return new Column($this->getColumnConfig());
    }
}
