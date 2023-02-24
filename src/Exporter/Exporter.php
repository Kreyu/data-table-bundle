<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;

class Exporter implements ExporterInterface
{
    public function __construct(
        private string $name,
        private ResolvedExporterTypeInterface $type,
        private array $options = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOption(string $name, mixed $default = null): mixed
    {
        return $this->options[$name] ?? $default;
    }

    public function export(DataTableView $view, string $filename): ExportFile
    {
        return $this->type->getInnerType()->export($view, $filename, $this->options);
    }
}
