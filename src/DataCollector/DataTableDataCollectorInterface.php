<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\VarDumper\Cloner\Data;

interface DataTableDataCollectorInterface extends DataCollectorInterface
{
    public function collectDataTable(DataTableInterface $dataTable): void;

    public function collectDataTableView(DataTableInterface $dataTable, DataTableView $view): void;

    public function collectColumnHeaderView(ColumnInterface $column, ColumnHeaderView $view): void;

    public function collectColumnValueView(ColumnInterface $column, ColumnValueView $view): void;

    public function collectSortingData(DataTableInterface $dataTable, SortingData $data): void;

    public function collectFilterView(FilterInterface $filter, FilterView $view): void;

    public function collectFiltrationData(DataTableInterface $dataTable, FiltrationData $data): void;

    public function collectActionView(ActionInterface $action, ActionView $view): void;

    public function getData(): array|Data;
}
