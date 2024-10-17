<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector;

use Kreyu\Bundle\DataTableBundle\Action\ActionContext;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\Caster\ClassStub;
use Symfony\Component\VarDumper\Cloner\Data;

class DataTableDataCollector extends AbstractDataCollector implements DataTableDataCollectorInterface
{
    public function __construct(
        private DataTableDataExtractorInterface $dataExtractor,
        private int $maxDepth = 3,
    ) {
        if (!class_exists(ClassStub::class)) {
            throw new \LogicException(sprintf('The VarDumper component is needed for using the "%s" class. Install symfony/var-dumper version 3.4 or above.', __CLASS__));
        }
    }

    public function __sleep(): array
    {
        $this->data = $this->cloneVar($this->data)->withMaxDepth($this->maxDepth);

        return parent::__sleep();
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
    }

    public function collectDataTable(DataTableInterface $dataTable): void
    {
        $data = [
            'columns' => $this->mapWithKeys(
                fn (ColumnInterface $column) => [$column->getName() => $this->dataExtractor->extractColumnConfiguration($column)],
                $dataTable->getColumns(),
            ),
            'filters' => $this->mapWithKeys(
                fn (FilterInterface $filter) => [$filter->getName() => $this->dataExtractor->extractFilterConfiguration($filter)],
                $dataTable->getFilters(),
            ),
            'actions' => $this->mapWithKeys(
                fn (ActionInterface $action) => [$action->getName() => $this->dataExtractor->extractActionConfiguration($action)],
                $dataTable->getActions(),
            ),
            'row_actions' => $this->mapWithKeys(
                fn (ActionInterface $action) => [$action->getName() => $this->dataExtractor->extractActionConfiguration($action)],
                $dataTable->getRowActions(),
            ),
            'batch_actions' => $this->mapWithKeys(
                fn (ActionInterface $action) => [$action->getName() => $this->dataExtractor->extractActionConfiguration($action)],
                $dataTable->getBatchActions(),
            ),
            'exporters' => $this->mapWithKeys(
                fn (ExporterInterface $exporter) => [$exporter->getName() => $this->dataExtractor->extractExporterConfiguration($exporter)],
                $dataTable->getExporters(),
            ),
        ];

        $data = array_merge($data, $this->dataExtractor->extractDataTableConfiguration($dataTable));

        $this->data[$dataTable->getName()] = $data;
    }

    public function collectDataTableView(DataTableInterface $dataTable, DataTableView $view): void
    {
        $this->data[$dataTable->getName()] += [
            'view_vars' => $this->ksort($view->vars),
            'value_rows' => $this->dataExtractor->extractValueRows($view),
        ];
    }

    public function collectColumnHeaderView(ColumnInterface $column, ColumnHeaderView $view): void
    {
        $this->data[$column->getDataTable()->getName()]['columns'][$column->getName()]['header_view_vars'] = $this->ksort($view->vars);
    }

    public function collectColumnValueView(ColumnInterface $column, ColumnValueView $view): void
    {
        // TODO: Support nested columns from CollectionColumnType
        if (null !== $view->parent->origin) {
            return;
        }

        $this->data[$column->getDataTable()->getName()]['columns'][$column->getName()]['value_view_vars'] = $this->ksort($view->vars);
    }

    public function collectSortingData(DataTableInterface $dataTable, SortingData $data): void
    {
        foreach ($data->getColumns() as $columnName => $columnSortingData) {
            if (!$dataTable->hasColumn($columnName)) {
                continue;
            }

            $column = $dataTable->getColumn($columnName);

            $this->data[$column->getDataTable()->getName()]['columns'][$column->getName()] ??= [];
            $this->data[$column->getDataTable()->getName()]['columns'][$column->getName()] += [
                'sort_direction' => $columnSortingData->getDirection(),
            ];
        }
    }

    public function collectPaginationData(DataTableInterface $dataTable, PaginationData $data): void
    {
        $this->data[$dataTable->getName()]['page'] = $data->getPage();
        $this->data[$dataTable->getName()]['per_page'] = $data->getPerPage();
        $this->data[$dataTable->getName()]['total_count'] = $dataTable->getPagination()->getTotalItemCount();
    }

    public function collectFilterView(FilterInterface $filter, FilterView $view): void
    {
        $this->data[$filter->getDataTable()->getName()]['filters'][$filter->getName()]['view_vars'] = $this->ksort($view->vars);
    }

    public function collectFiltrationData(DataTableInterface $dataTable, FiltrationData $data): void
    {
        foreach ($data->getFilters() as $filterName => $filterData) {
            if (!$dataTable->hasFilter($filterName)) {
                continue;
            }

            $filter = $dataTable->getFilter($filterName);

            $this->data[$filter->getDataTable()->getName()]['filters'][$filter->getName()] ??= [];
            $this->data[$filter->getDataTable()->getName()]['filters'][$filter->getName()] += [
                'data' => $filterData,
                'operator_label' => $filterData->getOperator()?->getLabel(),
            ];
        }
    }

    public function collectActionView(ActionInterface $action, ActionView $view): void
    {
        $actionsKey = match ($action->getConfig()->getContext()) {
            ActionContext::Global => 'actions',
            ActionContext::Row => 'row_actions',
            ActionContext::Batch => 'batch_actions',
        };

        $this->data[$action->getDataTable()->getName()][$actionsKey][$action->getName()]['view_vars'] = $this->ksort($view->vars);
    }

    public static function getTemplate(): ?string
    {
        return '@KreyuDataTable/data_collector/template.html.twig';
    }

    public function getData(): array|Data
    {
        return $this->data;
    }

    /**
     * @internal
     */
    private function mapWithKeys(callable $callback, array $array): array
    {
        $data = [];

        foreach ($array as $value) {
            foreach ($callback($value) as $mapKey => $mapValue) {
                $data[$mapKey] = $mapValue;
            }
        }

        return $data;
    }

    /**
     * @internal
     */
    private function ksort(array $array): array
    {
        $copy = $array;

        ksort($copy);

        return $copy;
    }
}
