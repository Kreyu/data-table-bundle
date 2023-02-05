<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

class ExporterFactory implements ExporterFactoryInterface
{
    public function __construct(
        private ExporterRegistryInterface $registry,
    ) {
    }

    public function create(string $name, string $type, array $options = []): ExporterInterface
    {
        $type = $this->registry->getType($type);

        $optionsResolver = $type->getOptionsResolver();

        return new Exporter($name, $type, $optionsResolver->resolve($options));
    }
}