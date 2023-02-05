<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;

interface ExporterFactoryInterface
{
    /**
     * @param class-string<ExporterTypeInterface> $type
     */
    public function create(string $name, string $type, array $options = []): ExporterInterface;
}