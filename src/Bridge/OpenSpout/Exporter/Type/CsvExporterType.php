<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type;

use OpenSpout\Writer\CSV;

class CsvExporterType extends AbstractOpenSpoutExporterType
{
    protected function getWriterClass(): string
    {
        return CSV\Writer::class;
    }

    protected function getOptionsClass(): string
    {
        return CSV\Options::class;
    }

    protected function getExtension(): string
    {
        return 'csv';
    }
}
