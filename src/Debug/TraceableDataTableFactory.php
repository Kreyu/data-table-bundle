<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Debug;

use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollector;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;

class TraceableDataTableFactory implements DataTableFactoryInterface
{
    public function __construct(
        private DataTableFactoryInterface $dataTableFactory,
        private DataTableDataCollector $dataCollector,
    ) {
    }

    public function create(string $type, mixed $data = null, array $options = []): DataTableInterface
    {
        $dataTable = $this->dataTableFactory->create($type, $data, $options);

        $this->dataCollector->collectDataTable($dataTable);

        return $dataTable;
    }

    public function createNamed(string $name, string $type, mixed $data = null, array $options = []): DataTableInterface
    {
        $dataTable = $this->dataTableFactory->createNamed($name, $type, $data, $options);

        $this->dataCollector->collectDataTable($dataTable);

        return $dataTable;
    }

    public function createBuilder(string $type, mixed $data = null, array $options = []): DataTableBuilderInterface
    {
        return $this->dataTableFactory->createBuilder($type, $data, $options);
    }

    public function createNamedBuilder(string $name, string $type, mixed $data = null, array $options = []): DataTableBuilderInterface
    {
        return $this->dataTableFactory->createNamedBuilder($name, $type, $data, $options);
    }
}
