<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Extension;

use Kreyu\Bundle\DataTableBundle\Exporter\ExporterBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractExporterTypeExtension implements ExporterTypeExtensionInterface
{
    public function buildExporter(ExporterBuilderInterface $builder, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
