<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Extension\ActionTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

class ActionRegistry implements ActionRegistryInterface
{
    /**
     * @var array<string, ActionTypeInterface>
     */
    private array $types = [];

    /**
     * @var array<ResolvedActionTypeInterface>
     */
    private array $resolvedTypes = [];

    /**
     * @var array<string, bool>
     */
    private array $checkedTypes = [];

    /**
     * @var array<string, ActionTypeExtensionInterface>
     */
    private array $typeExtensions = [];

    /**
     * @param iterable<ActionTypeInterface>          $types
     * @param iterable<ActionTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        iterable $types,
        iterable $typeExtensions,
        private ResolvedActionTypeFactoryInterface $resolvedColumnTypeFactory,
    ) {
        foreach ($types as $type) {
            if (!$type instanceof ActionTypeInterface) {
                throw new UnexpectedTypeException($type, ActionTypeInterface::class);
            }

            $this->types[$type::class] = $type;
        }

        foreach ($typeExtensions as $typeExtension) {
            if (!$typeExtension instanceof ActionTypeExtensionInterface) {
                throw new UnexpectedTypeException($typeExtension, ActionTypeExtensionInterface::class);
            }

            $this->typeExtensions[$typeExtension::class] = $typeExtension;
        }
    }

    public function getType(string $name): ResolvedActionTypeInterface
    {
        if (!isset($this->resolvedTypes[$name])) {
            if (!isset($this->types[$name])) {
                throw new \InvalidArgumentException(sprintf('Could not load action type "%s".', $name));
            }

            $this->resolvedTypes[$name] = $this->resolveType($this->types[$name]);
        }

        return $this->resolvedTypes[$name];
    }

    private function resolveType(ActionTypeInterface $type): ResolvedActionTypeInterface
    {
        $fqcn = $type::class;

        if (isset($this->checkedTypes[$fqcn])) {
            $types = implode(' > ', array_merge(array_keys($this->checkedTypes), [$fqcn]));
            throw new \LogicException(sprintf('Circular reference detected for action type "%s" (%s).', $fqcn, $types));
        }

        $this->checkedTypes[$fqcn] = true;

        $typeExtensions = array_filter(
            $this->typeExtensions,
            fn (ActionTypeExtensionInterface $extension) => $this->isFqcnExtensionEligible($fqcn, $extension),
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

    private function isFqcnExtensionEligible(string $fqcn, ActionTypeExtensionInterface $extension): bool
    {
        $extendedTypes = $extension::getExtendedTypes();

        if ($extendedTypes instanceof \Traversable) {
            $extendedTypes = iterator_to_array($extendedTypes);
        }

        return in_array($fqcn, $extendedTypes);
    }
}
