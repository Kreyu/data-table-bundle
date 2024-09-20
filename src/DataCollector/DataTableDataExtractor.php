<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use Symfony\Component\VarDumper\Caster\ClassStub;

class DataTableDataExtractor implements DataTableDataExtractorInterface
{
    public function extractDataTableConfiguration(DataTableInterface $dataTable): array
    {
        $data = [
            'name' => $dataTable->getName(),
            'type_class' => new ClassStub($dataTable->getConfig()->getType()->getInnerType()::class),
            'passed_options' => $dataTable->getConfig()->getAttribute('data_collector/passed_options', []),
            'resolved_options' => $dataTable->getConfig()->getOptions(),
            'features' => [
                'pagination' => [
                    'enabled' => $dataTable->getConfig()->isPaginationEnabled(),
                    'persistence_enabled' => $dataTable->getConfig()->isPaginationPersistenceEnabled(),
                ],
                'sorting' => [
                    'enabled' => $dataTable->getConfig()->isSortingEnabled(),
                    'persistence_enabled' => $dataTable->getConfig()->isSortingPersistenceEnabled(),
                ],
                'filtration' => [
                    'enabled' => $dataTable->getConfig()->isFiltrationEnabled(),
                    'persistence_enabled' => $dataTable->getConfig()->isFiltrationPersistenceEnabled(),
                ],
                'exporting' => [
                    'enabled' => $dataTable->getConfig()->isExportingEnabled(),
                ],
                'personalization' => [
                    'enabled' => $dataTable->getConfig()->isPersonalizationEnabled(),
                    'persistence_enabled' => $dataTable->getConfig()->isPersonalizationPersistenceEnabled(),
                ],
            ],
            'page' => $dataTable->getPagination()->getCurrentPageNumber(),
            'per_page' => $dataTable->getPagination()->getItemNumberPerPage(),
            'total_count' => $dataTable->getPagination()->getTotalItemCount(),
        ];

        ksort($data['passed_options']);
        ksort($data['resolved_options']);

        return $data;
    }

    public function extractColumnConfiguration(ColumnInterface $column): array
    {
        $data = [
            'name' => $column->getName(),
            'type_class' => new ClassStub($column->getConfig()->getType()->getInnerType()::class),
            'passed_options' => $column->getConfig()->getAttribute('data_collector/passed_options', []),
            'resolved_options' => $column->getConfig()->getOptions(),
        ];

        ksort($data['passed_options']);
        ksort($data['resolved_options']);

        return $data;
    }

    public function extractFilterConfiguration(FilterInterface $filter): array
    {
        $data = [
            'name' => $filter->getName(),
            'type_class' => new ClassStub($filter->getConfig()->getType()->getInnerType()::class),
            'passed_options' => $filter->getConfig()->getAttribute('data_collector/passed_options', []),
            'resolved_options' => $filter->getConfig()->getOptions(),
        ];

        ksort($data['passed_options']);
        ksort($data['resolved_options']);

        return $data;
    }

    public function extractActionConfiguration(ActionInterface $action): array
    {
        $data = [
            'name' => $action->getName(),
            'type_class' => new ClassStub($action->getConfig()->getType()->getInnerType()::class),
            'passed_options' => $action->getConfig()->getAttribute('data_collector/passed_options', []),
            'resolved_options' => $action->getConfig()->getOptions(),
        ];

        ksort($data['passed_options']);
        ksort($data['resolved_options']);

        return $data;
    }

    public function extractExporterConfiguration(ExporterInterface $exporter): array
    {
        $data = [
            'name' => $exporter->getName(),
            'type_class' => new ClassStub($exporter->getConfig()->getType()->getInnerType()::class),
            'passed_options' => $exporter->getConfig()->getAttribute('data_collector/passed_options', []),
            'resolved_options' => $exporter->getConfig()->getOptions(),
        ];

        ksort($data['passed_options']);
        ksort($data['resolved_options']);

        return $data;
    }

    public function extractValueRows(DataTableView $view): array
    {
        $data = [];

        foreach ($view->valueRows as $valueRow) {
            $data[] = $valueRow->data;
        }

        return $data;
    }
}
