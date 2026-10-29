<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Exporter;

use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;

abstract class ExporterTypeTestCase extends ExporterIntegrationTestCase
{
    /**
     * @return class-string<ExporterTypeInterface>
     */
    abstract protected function getTestedType(): string;

    protected function createExporter(array $options = []): ExporterInterface
    {
        return $this->factory->create($this->getTestedType(), $options);
    }

    protected function createNamedExporter(string $name, array $options = []): ExporterInterface
    {
        return $this->factory->createNamed($name, $this->getTestedType(), $options);
    }

    /**
     * @param array<array-key, string>              $headers
     * @param list<array<array-key, mixed>>         $valueRows
     */
    protected function createDataTableView(array $headers = [], array $valueRows = []): DataTableView
    {
        $view = new DataTableView();
        $headerRow = new HeaderRowView($view);
        $view->headerRow = $headerRow;

        foreach ($headers as $columnKey => $label) {
            $columnHeaderView = new ColumnHeaderView($headerRow);
            $columnHeaderView->vars['label'] = $label;

            $headerRow->children[self::getColumnName($columnKey)] = $columnHeaderView;
        }

        $valueRowViews = [];

        foreach ($valueRows as $rowIndex => $data) {
            $valueRow = new ValueRowView($view, $rowIndex, $data);

            foreach (array_keys($data) as $columnKey) {
                $valueView = new ColumnValueView($valueRow);
                $valueView->data = $data[$columnKey];
                $valueView->value = $data[$columnKey];

                $valueRow->children[self::getColumnName($columnKey)] = $valueView;
            }

            $valueRowViews[] = $valueRow;
        }

        $view->valueRows = $valueRowViews;

        return $view;
    }

    private static function getColumnName(int|string $columnKey): string
    {
        return sprintf('column_%s', $columnKey);
    }
}
