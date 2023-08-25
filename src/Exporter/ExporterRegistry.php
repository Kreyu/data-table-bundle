<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Exporter\Extension\ExporterTypeExtensionInterface;
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

    /**
     * @var array<string, ExporterTypeExtensionInterface>
     */
    private array $typeExtensions = [];

    /**
     * @param iterable<ExporterTypeInterface>        $types
     * @param iterable<ColumnTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        iterable $types,
        iterable $typeExtensions,
        private ResolvedExporterTypeFactoryInterface $resolvedExporterTypeFactory,
    ) {
        foreach ($types as $type) {
            if (!$type instanceof ExporterTypeInterface) {
                throw new UnexpectedTypeException($type, ExporterTypeInterface::class);
            }

            $this->types[$type::class] = $type;
        }

        foreach ($typeExtensions as $typeExtension) {
            if (!$typeExtension instanceof ExporterTypeExtensionInterface) {
                throw new UnexpectedTypeException($typeExtension, ExporterTypeExtensionInterface::class);
            }

            $this->typeExtensions[$typeExtension::class] = $typeExtension;
        }
    }

    public function getType(string $name): ResolvedExporterTypeInterface
    {
        if (!isset($this->resolvedTypes[$name])) {
            if (!isset($this->types[$name])) {
                throw new \InvalidArgumentException(sprintf('Could not load exporter type "%s".', $name));
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

        $typeExtensions = array_filter(
            $this->typeExtensions,
            fn (ExporterTypeExtensionInterface $extension) => $this->isFqcnExtensionEligible($fqcn, $extension),
        );

        $parentType = $type->getParent();

        try {
            return $this->resolvedExporterTypeFactory->createResolvedType(
                $type,
                $typeExtensions,
                $parentType ? $this->getType($parentType) : null,
            );
        } finally {
            unset($this->checkedTypes[$fqcn]);
        }
    }

    private function isFqcnExtensionEligible(string $fqcn, ExporterTypeExtensionInterface $extension): bool
    {
        $extendedTypes = $extension::getExtendedTypes();

        if ($extendedTypes instanceof \Traversable) {
            $extendedTypes = iterator_to_array($extendedTypes);
        }

        return in_array($fqcn, $extendedTypes);
    }
}
