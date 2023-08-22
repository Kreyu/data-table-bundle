<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;

class Column implements ColumnInterface
{
    private ?DataTableInterface $dataTable = null;

    public function __construct(
        private readonly ColumnConfigInterface $config,
    ) {
    }

    public function getName(): string
    {
        return $this->config->getName();
    }

    public function getConfig(): ColumnConfigInterface
    {
        return $this->config;
    }

    public function getDataTable(): DataTableInterface
    {
        if (null === $this->dataTable) {
            throw new BadMethodCallException('Column is not attached to any data table.');
        }

        return $this->dataTable;
    }

    public function setDataTable(DataTableInterface $dataTable): static
    {
        $this->dataTable = $dataTable;

        return $this;
    }

    public function createHeaderView(HeaderRowView $parent = null): ColumnHeaderView
    {
        $view = $this->config->getType()->createHeaderView($this, $parent);

        $this->config->getType()->buildHeaderView($view, $this, $this->config->getOptions());

        return $view;
    }

    public function createValueView(ValueRowView $parent = null): ColumnValueView
    {
        $view = $this->config->getType()->createValueView($this, $parent);

        $this->config->getType()->buildValueView($view, $this, $this->config->getOptions());

        return $view;
    }

    public function createExportHeaderView(HeaderRowView $parent = null): ColumnHeaderView
    {
        $view = $this->config->getType()->createExportHeaderView($this, $parent);

        $this->config->getType()->buildExportHeaderView($view, $this, $this->config->getOptions());

        return $view;
    }

    public function createExportValueView(ValueRowView $parent = null): ColumnValueView
    {
        $view = $this->config->getType()->createExportValueView($this, $parent);

        $this->config->getType()->buildExportValueView($view, $this, $this->config->getOptions());

        return $view;
    }
}
