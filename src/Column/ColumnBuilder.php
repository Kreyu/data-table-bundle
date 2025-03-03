<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;

class ColumnBuilder extends ColumnConfigBuilder implements ColumnBuilderInterface
{
    public function getColumn(): ColumnInterface
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return new Column($this->getColumnConfig());
    }

    private function createBuilderLockedException(): BadMethodCallException
    {
        return new BadMethodCallException('ColumnBuilder methods cannot be accessed anymore once the builder is turned into a ColumnConfigInterface instance.');
    }
}
