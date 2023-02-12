<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

class ColumnRegistry implements ColumnRegistryInterface
{
    /**
     * @var array<string, ColumnTypeInterface>
     */
    private array $types = [];

    /**
     * @var array<ResolvedColumnTypeInterface>
     */
    private array $resolvedTypes = [];

    /**
     * @var array<string, bool>
     */
    private array $checkedTypes = [];

    /**
     * @var array<string, ColumnTypeExtensionInterface>
     */
    private array $typeExtensions = [];

    /**
     * @param iterable<ColumnTypeInterface>          $types
     * @param iterable<ColumnTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        iterable $types,
        iterable $typeExtensions,
        private ResolvedColumnTypeFactoryInterface $resolvedColumnTypeFactory,
    ) {
        foreach ($types as $type) {
            if (!$type instanceof ColumnTypeInterface) {
                throw new UnexpectedTypeException($type, ColumnTypeInterface::class);
            }

            $this->types[$type::class] = $type;
        }

        foreach ($typeExtensions as $typeExtension) {
            if (!$typeExtension instanceof ColumnTypeExtensionInterface) {
                throw new UnexpectedTypeException($typeExtension, ColumnTypeExtensionInterface::class);
            }

            $this->typeExtensions[$typeExtension::class] = $typeExtension;
        }
    }

    public function getType(string $name): ResolvedColumnTypeInterface
    {
        if (!isset($this->resolvedTypes[$name])) {
            if (!isset($this->types[$name])) {
                throw new \InvalidArgumentException(sprintf('Could not load type "%s".', $name));
            }

            $this->resolvedTypes[$name] = $this->resolveType($this->types[$name]);
        }

        return $this->resolvedTypes[$name];
    }

    private function resolveType(ColumnTypeInterface $type): ResolvedColumnTypeInterface
    {
        $fqcn = $type::class;

        if (isset($this->checkedTypes[$fqcn])) {
            $types = implode(' > ', array_merge(array_keys($this->checkedTypes), [$fqcn]));
            throw new \LogicException(sprintf('Circular reference detected for column type "%s" (%s).', $fqcn, $types));
        }

        $this->checkedTypes[$fqcn] = true;

        $typeExtensions = array_filter(
            $this->typeExtensions,
            fn (ColumnTypeExtensionInterface $extension) => in_array($fqcn, $extension::getExtendedTypes()),
        );

        $parentType = $type->getParent();

        try {
            return $this->resolvedColumnTypeFactory->createResolvedType(
                $type,
                $typeExtensions,
                $parentType ? $this->getType($parentType) : null,
            );
        } finally {
            unset($this->checkedTypes[$fqcn]);
        }
    }
}
