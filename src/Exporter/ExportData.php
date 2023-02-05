<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

class ExportData
{
    public ExporterInterface $exporter;
    public ExportStrategy $strategy;
    public bool $includePersonalization;


    public function getExporter(): ExporterInterface
    {
        return $this->exporter;
    }

    public function setExporter(ExporterInterface $exporter): void
    {
        $this->exporter = $exporter;
    }

    public function getStrategy(): ExportStrategy
    {
        return $this->strategy;
    }

    public function setStrategy(ExportStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function isIncludePersonalization(): bool
    {
        return $this->includePersonalization;
    }

    public function setIncludePersonalization(bool $includePersonalization): void
    {
        $this->includePersonalization = $includePersonalization;
    }
}