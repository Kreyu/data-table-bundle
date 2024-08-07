<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\Caster\ClassStub;
use Symfony\Component\VarDumper\Cloner\Data;

class DataTableDataCollector extends AbstractDataCollector implements DataTableDataCollectorInterface
{
    public function __construct(
        readonly private DataTableDataExtractorInterface $dataExtractor,
    ) {
        if (!class_exists(ClassStub::class)) {
            throw new \LogicException(sprintf('The VarDumper component is needed for using the "%s" class. Install symfony/var-dumper version 3.4 or above.', __CLASS__));
        }
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        // Everything is collected on dataTable creation
    }

    public function collectDataTable(DataTableInterface $dataTable): void
    {
        $this->data[$dataTable->getConfig()->getName()] = [
            'filters' => [],
            'columns' => array_map(function (ColumnInterface $column) {
                return [
                    'name' => $column->getName(),
                    'type' => $column->getConfig()->getType()->getInnerType()::class,
                    'options' => $this->cloneVar($column->getConfig()->getOptions()),
                ];
            }, array_filter($dataTable->getColumns(), function (ColumnInterface $column) {
                return !str_contains($column->getName(), '__');
            }),
            ),
            'actions' => array_map(function (ActionInterface $action) {
                return [
                    'name' => $action->getName(),
                    'type' => $action->getConfig()->getType()->getInnerType()::class,
                    'options' => $this->cloneVar($action->getConfig()->getOptions()),
                ];
            }, $dataTable->getActions()),
            'batch_actions' => array_map(function (ActionInterface $action) {
                return [
                    'name' => $action->getName(),
                    'type' => $action->getConfig()->getType()->getInnerType()::class,
                    'options' => $this->cloneVar($action->getConfig()->getOptions()),
                ];
            }, $dataTable->getBatchActions()),
            'row_actions' => array_map(function (ActionInterface $action) {
                return [
                    'name' => $action->getName(),
                    'type' => $action->getConfig()->getType()->getInnerType()::class,
                    'options' => $this->cloneVar($action->getConfig()->getOptions()),
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

    public function collectFilter(DataTableInterface $dataTable, FiltrationData $filtrationData): void
    {
        $dataToRedirect = [];

        foreach ($filtrationData->getFilters() as $field => $data) {
            $dataToRedirect[] = $this->dataExtractor->extractFilter($dataTable, $field, $data);
        }

        $this->data[$dataTable->getConfig()->getName()]['filters'] = $dataToRedirect;
    }

    public function getData(): array|Data
    {
        return $this->data;
    }
}
