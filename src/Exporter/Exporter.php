<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;
use Symfony\Component\HttpFoundation\File\File;

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

    public function export(DataTableView $view): File
    {
        return $this->type->getInnerType()->export($view, $this->options);
    }
}