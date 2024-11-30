<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactory;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;

/**
 * Column factory that automatically sets the data table on created column.
 */
class TestColumnFactory extends ColumnFactory
{
    private DataTableInterface $dataTable;

    public function create(string $type = ColumnType::class, array $options = []): ColumnInterface
    {
        $column = parent::create($type, $options);
        $column->setDataTable($this->dataTable);

        return $column;
    }

    public function createNamed(string $name, string $type = ColumnType::class, array $options = []): ColumnInterface
    {
        $column = parent::createNamed($name, $type, $options);
        $column->setDataTable($this->dataTable);

        return $column;
    }

    public function setDataTable(DataTableInterface $dataTable): self
    {
        $this->dataTable = $dataTable;

        return $this;
    }
}
