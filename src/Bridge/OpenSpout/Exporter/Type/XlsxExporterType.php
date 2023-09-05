<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type;

use OpenSpout\Writer\XLSX;

class XlsxExporterType extends AbstractOpenSpoutExporterType
{
    protected function getWriterClass(): string
    {
        return XLSX\Writer::class;
    }

    protected function getOptionsClass(): string
    {
        return XLSX\Options::class;
    }

    protected function getExtension(): string
    {
        return 'xlsx';
    }
}
