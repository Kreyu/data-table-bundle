<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\AbstractExporterType as BaseAbstractExporterType;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use OpenSpout\Writer\WriterInterface;

abstract class AbstractExporterType extends BaseAbstractExporterType
{
    /**
     * @throws IOException
     * @throws WriterNotOpenedException
     */
    public function export(DataTableView $view, string $filename, array $options = []): ExportFile
    {
        if (!class_exists(Row::class)) {
            throw new \LogicException('Trying to use exporter that requires OpenSpout which is not installed. Try running "composer require openspout/openspout".');
        }

        touch($path = $this->getTempnam($options));

        $writer = $this->getWriter($options);
        $writer->openToFile($path);

        foreach ($view->vars['value_rows'] as $valueRow) {
            $values = [];

            foreach ($valueRow->children as $child) {
                if (false !== $child->vars['export']) {
                    $values[] = $child->vars['value'];
                }
            }

            $writer->addRow(Row::fromValues($values));
        }

        $writer->close();

        return new ExportFile($path, "$filename.csv");
    }

    protected abstract function getWriter(array $options): WriterInterface;
}