<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;

class FilterRegistry implements FilterRegistryInterface
{
    /**
     * @var array<string, FilterTypeInterface>
     */
    private array $types = [];

    /**
     * @var array<ResolvedFilterTypeInterface>
     */
    private array $resolvedTypes = [];

    /**
     * @var array<string, bool>
     */
    private array $checkedTypes = [];

    /**
     * @var array<string, FilterTypeExtensionInterface>
     */
    private array $typeExtensions = [];

    /**
     * @param iterable<FilterTypeInterface>          $types
     * @param iterable<FilterTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        iterable $types,
        iterable $typeExtensions,
        private ResolvedFilterTypeFactoryInterface $resolvedFilterTypeFactory,
    ) {
        foreach ($types as $type) {
            if (!$type instanceof FilterTypeInterface) {
                throw new UnexpectedTypeException($type, FilterTypeInterface::class);
            }

            $this->types[$type::class] = $type;
        }

        foreach ($typeExtensions as $typeExtension) {
            if (!$typeExtension instanceof FilterTypeExtensionInterface) {
                throw new UnexpectedTypeException($typeExtension, FilterTypeExtensionInterface::class);
            }

            $this->typeExtensions[$typeExtension::class] = $typeExtension;
        }
    }

    public function getType(string $name): ResolvedFilterTypeInterface
    {
        if (!isset($this->resolvedTypes[$name])) {
            if (!isset($this->types[$name])) {
                throw new \InvalidArgumentException(sprintf('Could not load filter type "%s".', $name));
            }

            $this->resolvedTypes[$name] = $this->resolveType($this->types[$name]);
        }

        return $this->resolvedTypes[$name];
    }

    private function resolveType(FilterTypeInterface $type): ResolvedFilterTypeInterface
    {
        $fqcn = $type::class;

        if (isset($this->checkedTypes[$fqcn])) {
            $types = implode(' > ', array_merge(array_keys($this->checkedTypes), [$fqcn]));
            throw new \LogicException(sprintf('Circular reference detected for filter type "%s" (%s).', $fqcn, $types));
        }

        $this->checkedTypes[$fqcn] = true;

        $typeExtensions = array_filter(
            $this->typeExtensions,
            fn (FilterTypeExtensionInterface $extension) => $this->isFqcnExtensionEligible($fqcn, $extension),
        );

        $parentType = $type->getParent();

        try {
            return $this->resolvedFilterTypeFactory->createResolvedType(
                $type,
                $typeExtensions,
                $parentType ? $this->getType($parentType) : null,
            );
        } finally {
            unset($this->checkedTypes[$fqcn]);
        }
    }

    private function isFqcnExtensionEligible(string $fqcn, FilterTypeExtensionInterface $extension): bool
    {
        $extendedTypes = $extension::getExtendedTypes();

        if ($extendedTypes instanceof \Traversable) {
            $extendedTypes = iterator_to_array($extendedTypes);
        }

        return in_array($fqcn, $extendedTypes);
    }
}
