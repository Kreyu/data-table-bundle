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
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Util\ArrayUtil;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\Caster\ClassStub;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * @psalm-type CollectedData = array<string, array{
 *     columns?: array<string, array<string, mixed>>,
 *     filters?: array<string, array<string, mixed>>,
 *     actions?: array<string, array<string, mixed>>,
 *     row_actions?: array<string, array<string, mixed>>,
 *     batch_actions?: array<string, array<string, mixed>>,
 *     ...<string, mixed>
 * }>
 */
class DataTableDataCollector extends AbstractDataCollector implements DataTableDataCollectorInterface
{
    public function __construct(
        private DataTableDataExtractorInterface $dataExtractor,
        private int $maxDepth = 3,
    ) {
        if (!class_exists(ClassStub::class)) {
            throw new \LogicException(sprintf(
                'The VarDumper component is needed for using the "%s" class. Install symfony/var-dumper version 3.4 or above.',
                __CLASS__,
            ));
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        $this->data = $this->cloneVar($this->data)->withMaxDepth($this->maxDepth);

        return ['data' => $this->data];
    }

    public static function getTemplate(): ?string
    {
        return '@KreyuDataTable/data_collector/template.html.twig';
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void {}

    public function collectDataTable(DataTableInterface $dataTable): void
    {
        $dataTableName = $dataTable->getName();
        $collectedData = $this->getCollectedData();

        $collectedData[$dataTableName] = array_merge(
            $collectedData[$dataTableName] ?? [],
            $this->dataExtractor->extractDataTableConfiguration($dataTable),
            [
                'columns' => ArrayUtil::mapWithKeys(
                    fn (ColumnInterface $column) => [
                        $column->getName() => $this->dataExtractor->extractColumnConfiguration($column),
                    ],
                    $dataTable->getColumns(),
                ),
                'filters' => ArrayUtil::mapWithKeys(
                    fn (FilterInterface $filter) => [
                        $filter->getName() => $this->dataExtractor->extractFilterConfiguration($filter),
                    ],
                    $dataTable->getFilters(),
                ),
                'actions' => ArrayUtil::mapWithKeys(
                    fn (ActionInterface $action) => [
                        $action->getName() => $this->dataExtractor->extractActionConfiguration($action),
                    ],
                    $dataTable->getActions(),
                ),
                'row_actions' => ArrayUtil::mapWithKeys(
                    fn (ActionInterface $action) => [
                        $action->getName() => $this->dataExtractor->extractActionConfiguration($action),
                    ],
                    $dataTable->getRowActions(),
                ),
                'batch_actions' => ArrayUtil::mapWithKeys(
                    fn (ActionInterface $action) => [
                        $action->getName() => $this->dataExtractor->extractActionConfiguration($action),
                    ],
                    $dataTable->getBatchActions(),
                ),
                'exporters' => ArrayUtil::mapWithKeys(
                    fn (ExporterInterface $exporter) => [
                        $exporter->getName() => $this->dataExtractor->extractExporterConfiguration($exporter),
                    ],
                    $dataTable->getExporters(),
                ),
            ],
        );

        $isPaginationEnabled = $dataTable->getConfig()->isPaginationEnabled();

        if (!$isPaginationEnabled) {
            $collectedData[$dataTableName]['total_count'] = $dataTable->isDeferred()
                ? null
                : iterator_count($dataTable->getItems());
        }

        $this->data = $collectedData;
    }

    public function collectDataTableView(DataTableInterface $dataTable, DataTableView $view): void
    {
        $collectedData = $this->getCollectedData();

        $collectedData[$dataTable->getName()] += [
            'view_vars' => ArrayUtil::ksort($view->vars),
            'value_rows' => $this->dataExtractor->extractValueRows($view),
        ];

        $this->data = $collectedData;
    }

    public function collectColumnHeaderView(ColumnInterface $column, ColumnHeaderView $view): void
    {
        $dataTableName = $column->getDataTable()->getName();
        $collectedData = $this->getCollectedData();

        $collectedData[$dataTableName]['columns'][$column->getName()]['header_view_vars'] = ArrayUtil::ksort($view->vars);

        $this->data = $collectedData;
    }

    public function collectColumnValueView(ColumnInterface $column, ColumnValueView $view): void
    {
        $isNestedColumnView = $view->parent->origin !== null;

        if ($isNestedColumnView) {
            return;
        }

        $dataTableName = $column->getDataTable()->getName();
        $collectedData = $this->getCollectedData();

        $collectedData[$dataTableName]['columns'][$column->getName()]['value_view_vars'] = ArrayUtil::ksort($view->vars);

        $this->data = $collectedData;
    }

    public function collectSortingData(DataTableInterface $dataTable, SortingData $data): void
    {
        $collectedData = $this->getCollectedData();

        foreach ($data->getColumns() as $columnSortingData) {
            $columnName = $columnSortingData->getName();
            $hasColumn = $dataTable->hasColumn($columnName);

            if (!$hasColumn) {
                continue;
            }

            $column = $dataTable->getColumn($columnName);
            $dataTableName = $column->getDataTable()->getName();

            $collectedData[$dataTableName]['columns'][$column->getName()] ??= [];
            $collectedData[$dataTableName]['columns'][$column->getName()] += [
                'sort_direction' => $columnSortingData->getDirection(),
            ];
        }

        $this->data = $collectedData;
    }

    public function collectPaginationData(DataTableInterface $dataTable, PaginationData $data): void
    {
        $dataTableName = $dataTable->getName();
        $collectedData = $this->getCollectedData();

        $collectedData[$dataTableName]['page'] = $data->getPage();
        $collectedData[$dataTableName]['per_page'] = $data->getPerPage();
        $collectedData[$dataTableName]['total_count'] = $dataTable->isDeferred()
            ? null
            : $dataTable->getPagination()->getTotalItemCount();

        $this->data = $collectedData;
    }

    public function collectFilterView(FilterInterface $filter, FilterView $view): void
    {
        $dataTableName = $filter->getDataTable()->getName();
        $collectedData = $this->getCollectedData();

        $collectedData[$dataTableName]['filters'][$filter->getName()]['view_vars'] = ArrayUtil::ksort($view->vars);

        $this->data = $collectedData;
    }

    public function collectFiltrationData(DataTableInterface $dataTable, FiltrationData $data): void
    {
        $collectedData = $this->getCollectedData();

        foreach ($data->getFilters() as $filterName => $filterFiltrationData) {
            $hasFilter = $dataTable->hasFilter($filterName);

            if (!$hasFilter) {
                continue;
            }

            $filter = $dataTable->getFilter($filterName);
            $dataTableName = $filter->getDataTable()->getName();

            $collectedData[$dataTableName]['filters'][$filter->getName()] ??= [];
            $collectedData[$dataTableName]['filters'][$filter->getName()] += [
                'data' => $filterFiltrationData,
                'operator_label' => $filterFiltrationData->getOperator()?->getLabel(),
            ];
        }

        $this->data = $collectedData;
    }

    public function collectActionView(ActionInterface $action, ActionView $view): void
    {
        $actionsKey = match ($action->getConfig()->getContext()) {
            ActionContext::Global => 'actions',
            ActionContext::Row => 'row_actions',
            ActionContext::Batch => 'batch_actions',
        };

        $dataTableName = $action->getDataTable()->getName();
        $collectedData = $this->getCollectedData();

        /** @var array<string, array<string, mixed>> $actionsData */
        $actionsData = $collectedData[$dataTableName][$actionsKey] ?? [];
        $actionsData[$action->getName()]['view_vars'] = ArrayUtil::ksort($view->vars);

        $collectedData[$dataTableName][$actionsKey] = $actionsData;

        $this->data = $collectedData;
    }

    public function getData(): array|Data
    {
        return $this->data;
    }

    /**
     * @return CollectedData
     */
    private function getCollectedData(): array
    {
        if ($this->data instanceof Data) {
            throw new LogicException('Data table data cannot be collected after the collector has been serialized.');
        }

        /** @var CollectedData */
        return $this->data;
    }
}
