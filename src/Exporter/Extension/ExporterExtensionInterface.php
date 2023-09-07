<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Extension;

use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;

interface ExporterExtensionInterface
{
    public function getType(string $name): ExporterTypeInterface;

    public function hasType(string $name): bool;

    public function getTypeExtensions(string $name): array;

    public function hasTypeExtensions(string $name): bool;
}
