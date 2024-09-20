<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;

interface ExporterConfigInterface
{
    public function getName(): string;

    public function getType(): ResolvedExporterTypeInterface;

    public function getOptions(): array;

    public function hasOption(string $name): bool;

    public function getOption(string $name, mixed $default = null): mixed;

    public function getAttributes(): array;

    public function hasAttribute(string $name): bool;

    public function getAttribute(string $name, mixed $default = null): mixed;
}
