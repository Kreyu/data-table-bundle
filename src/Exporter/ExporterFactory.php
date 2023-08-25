<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterType;

class ExporterFactory implements ExporterFactoryInterface
{
    public function __construct(
        private ExporterRegistryInterface $registry,
    ) {
    }

    public function create(string $type = ExporterType::class, array $options = []): ExporterInterface
    {
        return $this->createBuilder($type, $options)->getExporter();
    }

    public function createNamed(string $name, string $type = ExporterType::class, array $options = []): ExporterInterface
    {
        return $this->createNamedBuilder($name, $type, $options)->getExporter();
    }

    public function createBuilder(string $type = ExporterType::class, array $options = []): ExporterBuilderInterface
    {
        return $this->createNamedBuilder($this->registry->getType($type)->getName(), $type, $options);
    }

    public function createNamedBuilder(string $name, string $type = ExporterType::class, array $options = []): ExporterBuilderInterface
    {
        $type = $this->registry->getType($type);

        $builder = $type->createBuilder($this, $name, $options);

        $type->buildExporter($builder, $builder->getOptions());

        return $builder;
    }
}
