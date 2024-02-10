<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Extension\ActionTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

class ActionRegistry implements ActionRegistryInterface
{
    /**
     * @var array<ActionTypeInterface>
     */
    private array $types;

    /**
     * @var array<ActionTypeExtensionInterface>
     */
    private array $typeExtensions;

    /**
     * @var array<ResolvedActionTypeFactoryInterface>
     */
    private array $resolvedTypes;

    /**
     * @var array<class-string<ActionTypeInterface>, bool>
     */
    private array $checkedTypes;

    /**
     * @param iterable<ActionTypeInterface>          $types
     * @param iterable<ActionTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        iterable $types,
        iterable $typeExtensions,
        private readonly ResolvedActionTypeFactoryInterface $resolvedTypeFactory,
    ) {
        $this->setTypes($types);
        $this->setTypeExtensions($typeExtensions);
    }

    public function getType(string $name): ResolvedActionTypeInterface
    {
        return $this->resolvedTypes[$name] ??= $this->resolveType($name);
    }

    public function hasType(string $name): bool
    {
        return isset($this->types[$name]);
    }

    private function resolveType(string $name): ResolvedActionTypeInterface
    {
        $type = $this->types[$name] ?? throw new InvalidArgumentException(sprintf('The action type %s does not exist', $name));

        if (isset($this->checkedTypes[$fqcn = $type::class])) {
            $types = implode(' > ', array_merge(array_keys($this->checkedTypes), [$fqcn]));
            throw new LogicException(sprintf('Circular reference detected for action type "%s" (%s).', $fqcn, $types));
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
            if (!$type instanceof ActionTypeInterface) {
                throw new UnexpectedTypeException($type, ActionTypeInterface::class);
            }

            $this->types[$type::class] = $type;
        }
    }

    private function setTypeExtensions(iterable $typeExtensions): void
    {
        $this->typeExtensions = [];

        foreach ($typeExtensions as $typeExtension) {
            if (!$typeExtension instanceof ActionTypeExtensionInterface) {
                throw new UnexpectedTypeException($typeExtension, ActionTypeExtensionInterface::class);
            }

            foreach ($typeExtension::getExtendedTypes() as $extendedType) {
                $this->typeExtensions[$extendedType][] = $typeExtension;
            }
        }
    }
}
