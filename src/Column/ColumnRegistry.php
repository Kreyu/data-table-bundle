<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

class ColumnRegistry implements ColumnRegistryInterface
{
    /**
     * @var array<ColumnTypeInterface>
     */
    private array $types;

    /**
     * @var array<ColumnTypeExtensionInterface>
     */
    private array $typeExtensions;

    /**
     * @var array<ResolvedColumnTypeFactoryInterface>
     */
    private array $resolvedTypes;

    /**
     * @var array<class-string<ColumnTypeInterface>, bool>
     */
    private array $checkedTypes;

    /**
     * @param iterable<ColumnTypeInterface> $types
     * @param iterable<ColumnTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        iterable $types,
        iterable $typeExtensions,
        private readonly ResolvedColumnTypeFactoryInterface $resolvedTypeFactory,
    ) {
        $this->setTypes($types);
        $this->setTypeExtensions($typeExtensions);
    }

    public function getType(string $name): ResolvedColumnTypeInterface
    {
        return $this->resolvedTypes[$name] ??= $this->resolveType($name);
    }

    public function hasType(string $name): bool
    {
        return isset($this->types[$name]);
    }

    private function resolveType(string $name): ResolvedColumnTypeInterface
    {
        $type = $this->types[$name] ?? throw new InvalidArgumentException(sprintf('The column type %s does not exist', $name));

        if (isset($this->checkedTypes[$fqcn = $type::class])) {
            $types = implode(' > ', array_merge(array_keys($this->checkedTypes), [$fqcn]));
            throw new LogicException(sprintf('Circular reference detected for column type "%s" (%s).', $fqcn, $types));
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
            if (!$type instanceof ColumnTypeInterface) {
                throw new UnexpectedTypeException($type, ColumnTypeInterface::class);
            }

            $this->types[$type::class] = $type;
        }
    }

    private function setTypeExtensions(iterable $typeExtensions): void
    {
        $this->typeExtensions = [];

        foreach ($typeExtensions as $typeExtension) {
            if (!$typeExtension instanceof ColumnTypeExtensionInterface) {
                throw new UnexpectedTypeException($typeExtension, ColumnTypeExtensionInterface::class);
            }

            foreach ($typeExtension::getExtendedTypes() as $extendedType) {
                $this->typeExtensions[$extendedType][] = $typeExtension;
            }
        }
    }
}
