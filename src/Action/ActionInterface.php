<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;

interface ActionInterface
{
    public function getName(): string;

    public function getConfig(): ActionConfigInterface;

    public function getDataTable(): DataTableInterface;

    public function setDataTable(DataTableInterface $dataTable): static;

    public function createView(DataTableView|ColumnValueView $parent): ActionView;
}
