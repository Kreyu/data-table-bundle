<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

class ExportData
{
    public string $filename;
    public ExporterInterface $exporter;
    public ExportStrategy $strategy;
    public bool $includePersonalization;
}
