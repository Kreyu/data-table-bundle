<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;

abstract class DataTableTypeTestCase extends DataTableIntegrationTestCase
{
    /**
     * @return class-string<DataTableTypeInterface>
     */
    abstract protected function getTestedType(): string;

    protected function createDataTable(array $options = []): DataTableInterface
    {
        return $this->dataTableFactory->create($this->getTestedType(), $options);
    }

    protected function createNamedDataTable(string $name, array $options = []): DataTableInterface
    {
        return $this->dataTableFactory->createNamed($name, $this->getTestedType(), $options);
    }

    protected function createDataTableView(DataTableInterface $dataTable): DataTableView
    {
        return $dataTable->createView();
    }
}
