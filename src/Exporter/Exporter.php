<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;

class Exporter implements ExporterInterface
{
    private ?DataTableInterface $dataTable = null;

    public function __construct(
        private readonly ExporterConfigInterface $config,
    ) {
    }

    public function getName(): string
    {
        return $this->config->getName();
    }

    public function getConfig(): ExporterConfigInterface
    {
        return $this->config;
    }

    public function getDataTable(): DataTableInterface
    {
        if (null === $this->dataTable) {
            throw new BadMethodCallException('Exporter is not attached to any data table.');
        }

        return $this->dataTable;
    }

    public function setDataTable(DataTableInterface $dataTable): static
    {
        $this->dataTable = $dataTable;

        return $this;
    }

    public function export(DataTableView $view, string $filename = 'export'): ExportFile
    {
        return $this->config->getType()->getInnerType()->export($view, $this, $filename, $this->config->getOptions());
    }
}
