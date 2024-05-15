<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector;

use Kreyu\Bundle\DataTableBundle\Action\Action;
use Kreyu\Bundle\DataTableBundle\Column\Column;
use Kreyu\Bundle\DataTableBundle\DataTable;
use Kreyu\Bundle\DataTableBundle\Filter\Filter;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DataTableDataCollector extends AbstractDataCollector
{
    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        // Everything is collected on dataTable creation
    }

    public function collectDataTable(DataTable $dataTable): void
    {
        $this->data[$dataTable->getConfig()->getName()] = [
            'columns' => array_map(function (Column $column) {
                return [
                    'name' => $column->getName(),
                    'type' => $column->getConfig()->getType()->getInnerType()::class,
                ];
            }, array_filter($dataTable->getColumns(), function (Column $column) {
                return !str_contains($column->getName(), '__');
            }),
            ),
            'filters' => array_map(function (Filter $filter) {
                return [
                    'name' => $filter->getName(),
                    'type' => $filter->getConfig()->getType()->getInnerType()::class,
                    'operator' => $filter->getConfig()->getDefaultOperator()->name,
                ];
            }, $dataTable->getFilters()),
            'actions' => array_map(function (Action $action) {
                return [
                    'name' => $action->getName(),
                    'type' => $action->getConfig()->getType()->getInnerType()::class,
                    'options' => array_map(function (mixed $value) {
                        return is_callable($value) ? 'Closure' : $value;
                    }, $action->getConfig()->getOptions()),
                ];
            }, $dataTable->getActions()),
            'batch_actions' => array_map(function (Action $action) {
                return [
                    'name' => $action->getName(),
                    'type' => $action->getConfig()->getType()->getInnerType()::class,
                    'options' => array_map(function (mixed $value) {
                        return is_callable($value) ? 'Closure' : $value;
                    }, $action->getConfig()->getOptions()),
                ];
            }, $dataTable->getBatchActions()),
            'row_actions' => array_map(function (Action $action) {
                return [
                    'name' => $action->getName(),
                    'type' => $action->getConfig()->getType()->getInnerType()::class,
                    'options' => array_map(function (mixed $value) {
                        return is_callable($value) ? 'Closure' : $value;
                    }, $action->getConfig()->getOptions()),
                ];
            }, $dataTable->getRowActions()),
        ];
    }

    public static function getTemplate(): ?string
    {
        return '@KreyuDataTable/data_collector/template.html.twig';
    }

    public function getDataTables(): array
    {
        return array_keys($this->data);
    }

    public function getColumns(string $dataTableName): array
    {
        return $this->data[$dataTableName]['columns'];
    }

    public function getFilters(string $dataTableName): array
    {
        return $this->data[$dataTableName]['filters'];
    }

    public function getActions(string $dataTableName): array
    {
        return $this->data[$dataTableName]['actions'];
    }

    public function getBatchActions(string $dataTableName): array
    {
        return $this->data[$dataTableName]['batch_actions'];
    }

    public function getRowActions(string $dataTableName): array
    {
        return $this->data[$dataTableName]['row_actions'];
    }
}
