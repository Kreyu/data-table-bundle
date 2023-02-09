<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;

class ExporterRegistry implements ExporterRegistryInterface
{
    /**
     * @var array<string, ExporterTypeInterface>
     */
    private array $types = [];

    /**
     * @var array<ResolvedExporterTypeInterface>
     */
    private array $resolvedTypes = [];

    /**
     * @var array<string, bool>
     */
    private array $checkedTypes = [];

    public function __construct(
        iterable $types,
        private ResolvedExporterTypeFactoryInterface $resolvedExporterTypeFactory,
    ) {
        foreach ($types as $type) {
            if (!$type instanceof ExporterTypeInterface) {
                throw new \InvalidArgumentException();
            }

            $this->types[$type::class] = $type;
        }
    }

    public function getType(string $name): ResolvedExporterTypeInterface
    {
        if (!isset($this->resolvedTypes[$name])) {
            if (!isset($this->types[$name])) {
                throw new \InvalidArgumentException(sprintf('Could not load type "%s".', $name));
            }

            $this->resolvedTypes[$name] = $this->resolveType($this->types[$name]);
        }

        return $this->resolvedTypes[$name];
    }

    private function resolveType(ExporterTypeInterface $type): ResolvedExporterTypeInterface
    {
        $fqcn = $type::class;

        if (isset($this->checkedTypes[$fqcn])) {
            $types = implode(' > ', array_merge(array_keys($this->checkedTypes), [$fqcn]));
            throw new \LogicException(sprintf('Circular reference detected for filter type "%s" (%s).', $fqcn, $types));
        }

        $this->checkedTypes[$fqcn] = true;

        $parentType = $type->getParent();

        try {
            return $this->resolvedExporterTypeFactory->createResolvedType(
                $type,
                $parentType ? $this->getType($parentType) : null,
            );
        } finally {
            unset($this->checkedTypes[$fqcn]);
        }
    }
}
