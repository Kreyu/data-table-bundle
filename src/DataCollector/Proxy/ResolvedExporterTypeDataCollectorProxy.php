<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector\Proxy;

use Kreyu\Bundle\DataTableBundle\Exporter\ExporterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResolvedExporterTypeDataCollectorProxy implements ResolvedExporterTypeInterface
{
    public function __construct(
        private ResolvedExporterTypeInterface $proxiedType,
    ) {
    }

    public function getName(): string
    {
        return $this->proxiedType->getName();
    }

    public function getParent(): ?ResolvedExporterTypeInterface
    {
        return $this->proxiedType->getParent();
    }

    public function getInnerType(): ExporterTypeInterface
    {
        return $this->proxiedType->getInnerType();
    }

    public function getTypeExtensions(): array
    {
        return $this->proxiedType->getTypeExtensions();
    }

    public function createBuilder(ExporterFactoryInterface $factory, string $name, array $options): ExporterBuilderInterface
    {
        $builder = $this->proxiedType->createBuilder($factory, $name, $options);
        $builder->setAttribute('data_collector/passed_options', $options);
        $builder->setType($this);

        return $builder;
    }

    public function buildExporter(ExporterBuilderInterface $builder, array $options): void
    {
        $this->proxiedType->buildExporter($builder, $options);
    }

    public function getOptionsResolver(): OptionsResolver
    {
        return $this->proxiedType->getOptionsResolver();
    }
}
