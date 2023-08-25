<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;

interface ExporterConfigBuilderInterface extends ExporterConfigInterface
{
    public function setName(string $name): static;

    public function setType(ResolvedExporterTypeInterface $type): static;

    public function setOptions(array $options): static;

    public function setOption(string $name, mixed $value): static;

    public function getExporterConfig(): ExporterConfigInterface;
}
