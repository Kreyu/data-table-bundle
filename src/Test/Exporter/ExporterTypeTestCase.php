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

    protected function createDataTableView(array $headers = [], array $valueRows = []): DataTableView
    {
        $view = new DataTableView();
        $view->headerRow = new HeaderRowView($view);

        foreach ($headers as $label) {
            $headerRow = new ColumnHeaderView($view->headerRow);
            $headerRow->vars['label'] = $label;

            $view->headerRow->children[] = $headerRow;
        }

        foreach ($valueRows as $index => $data) {
            $valueRow = new ValueRowView($view, $index, $data);

            foreach ($data as $value) {
                $valueView = new ColumnValueView($valueRow);
                $valueView->data = $valueView->value = $value;

                $valueRow->children[] = $valueView;
            }

            $view->valueRows[] = $valueRow;
        }

        return $view;
    }
}
