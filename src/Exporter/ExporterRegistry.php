<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Exporter\Extension\ExporterTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;

class ExporterRegistry implements ExporterRegistryInterface
{
    /**
     * @var array<ExporterTypeInterface>
     */
    private array $types;

    /**
     * @var array<ExporterTypeExtensionInterface>
     */
    private array $typeExtensions;

    /**
     * @var array<ResolvedExporterTypeFactoryInterface>
     */
    private array $resolvedTypes;

    /**
     * @var array<class-string<ExporterTypeInterface>, bool>
     */
    private array $checkedTypes;

    /**
     * @param iterable<ExporterTypeInterface>          $types
     * @param iterable<ExporterTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        iterable $types,
        iterable $typeExtensions,
        private readonly ResolvedExporterTypeFactoryInterface $resolvedTypeFactory,
    ) {
        $this->setTypes($types);
        $this->setTypeExtensions($typeExtensions);
    }

    public function getType(string $name): ResolvedExporterTypeInterface
    {
        return $this->resolvedTypes[$name] ??= $this->resolveType($name);
    }

    public function hasType(string $name): bool
    {
        return isset($this->types[$name]);
    }

    private function resolveType(string $name): ResolvedExporterTypeInterface
    {
        $type = $this->types[$name] ?? throw new InvalidArgumentException(sprintf('The exporter type %s does not exist', $name));

        if (isset($this->checkedTypes[$fqcn = $type::class])) {
            $types = implode(' > ', array_merge(array_keys($this->checkedTypes), [$fqcn]));
            throw new LogicException(sprintf('Circular reference detected for exporter type "%s" (%s).', $fqcn, $types));
        }

        $this->checkedTypes[$fqcn] = true;

        $parentType = $type->getParent();

        try {
            return $this->resolvedTypeFactory->createResolvedType(
                type: $type,
                typeExtensions: $this->typeExtensions[$type::class] ?? [],
                parent: $parentType ? $this->getType($parentType) : null,
            );
        } finally {
            unset($this->checkedTypes[$fqcn]);
        }
    }

    private function setTypes(iterable $types): void
    {
        $this->types = [];

        foreach ($types as $type) {
            if (!$type instanceof ExporterTypeInterface) {
                throw new UnexpectedTypeException($type, ExporterTypeInterface::class);
            }

            $this->types[$type::class] = $type;
        }
    }

    private function setTypeExtensions(iterable $typeExtensions): void
    {
        $this->typeExtensions = [];

        foreach ($typeExtensions as $typeExtension) {
            if (!$typeExtension instanceof ExporterTypeExtensionInterface) {
                throw new UnexpectedTypeException($typeExtension, ExporterTypeExtensionInterface::class);
            }

            foreach ($typeExtension::getExtendedTypes() as $extendedType) {
                $this->typeExtensions[$extendedType][] = $typeExtension;
            }
        }
    }
}
