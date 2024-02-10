<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

class FilterRegistry implements FilterRegistryInterface
{
    /**
     * @var array<FilterTypeInterface>
     */
    private array $types;

    /**
     * @var array<FilterTypeExtensionInterface>
     */
    private array $typeExtensions;

    /**
     * @var array<ResolvedFilterTypeFactoryInterface>
     */
    private array $resolvedTypes;

    /**
     * @var array<class-string<FilterTypeInterface>, bool>
     */
    private array $checkedTypes;

    /**
     * @param iterable<FilterTypeInterface> $types
     * @param iterable<FilterTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        iterable $types,
        iterable $typeExtensions,
        private readonly ResolvedFilterTypeFactoryInterface $resolvedTypeFactory,
    ) {
        $this->setTypes($types);
        $this->setTypeExtensions($typeExtensions);
    }

    public function getType(string $name): ResolvedFilterTypeInterface
    {
        return $this->resolvedTypes[$name] ??= $this->resolveType($name);
    }

    public function hasType(string $name): bool
    {
        return isset($this->types[$name]);
    }

    private function resolveType(string $name): ResolvedFilterTypeInterface
    {
        $type = $this->types[$name] ?? throw new InvalidArgumentException(sprintf('The filter type %s does not exist', $name));

        if (isset($this->checkedTypes[$fqcn = $type::class])) {
            $types = implode(' > ', array_merge(array_keys($this->checkedTypes), [$fqcn]));
            throw new LogicException(sprintf('Circular reference detected for filter type "%s" (%s).', $fqcn, $types));
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
            if (!$type instanceof FilterTypeInterface) {
                throw new UnexpectedTypeException($type, FilterTypeInterface::class);
            }

            $this->types[$type::class] = $type;
        }
    }

    private function setTypeExtensions(iterable $typeExtensions): void
    {
        $this->typeExtensions = [];

        foreach ($typeExtensions as $typeExtension) {
            if (!$typeExtension instanceof FilterTypeExtensionInterface) {
                throw new UnexpectedTypeException($typeExtension, FilterTypeExtensionInterface::class);
            }

            foreach ($typeExtension::getExtendedTypes() as $extendedType) {
                $this->typeExtensions[$extendedType][] = $typeExtension;
            }
        }
    }
}
