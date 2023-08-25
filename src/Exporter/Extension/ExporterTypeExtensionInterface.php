<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Extension;

use Kreyu\Bundle\DataTableBundle\Exporter\ExporterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ExporterTypeExtensionInterface
{
    public function buildExporter(ExporterBuilderInterface $builder, array $options): void;

    public function configureOptions(OptionsResolver $resolver): void;

    /**
     * @return iterable<class-string<ExporterTypeInterface>>
     */
    public static function getExtendedTypes(): iterable;
}
