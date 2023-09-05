<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type;

use OpenSpout\Writer\ODS;

class OdsExporterType extends AbstractOpenSpoutExporterType
{
    protected function getWriterClass(): string
    {
        return ODS\Writer::class;
    }

    protected function getOptionsClass(): string
    {
        return ODS\Options::class;
    }

    protected function getExtension(): string
    {
        return 'ods';
    }
}
