<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension\Core;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Extension\AbstractTypeExtension;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Symfony\Component\Form\FormFactoryInterface;

class ExporterExtension extends AbstractTypeExtension
{
    public function __construct(
        private ExporterFactoryInterface $exporterFactory,
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->setExporterFactory($this->exporterFactory);

        if ($builder->isExportingEnabled()) {
            $builder->setExportFormFactory($this->formFactory);
        }
    }

    public static function getExtendedTypes(): iterable
    {
        return [DataTableType::class];
    }
}