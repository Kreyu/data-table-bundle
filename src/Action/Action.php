<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;

class Action implements ActionInterface
{
    private ?DataTableInterface $dataTable = null;

    public function __construct(
        private readonly ActionConfigInterface $config,
    ) {
    }

    public function getName(): string
    {
        return $this->config->getName();
    }

    public function getConfig(): ActionConfigInterface
    {
        return $this->config;
    }

    public function getDataTable(): DataTableInterface
    {
        if (null === $this->dataTable) {
            throw new BadMethodCallException('Action is not attached to any data table.');
        }

        return $this->dataTable;
    }

    public function setDataTable(DataTableInterface $dataTable): static
    {
        $this->dataTable = $dataTable;

        return $this;
    }

    public function createView(DataTableView|ColumnValueView $parent): ActionView
    {
        $view = $this->config->getType()->createView($this, $parent);

        $this->config->getType()->buildView($view, $this, $this->config->getOptions());

        return $view;
    }
}
