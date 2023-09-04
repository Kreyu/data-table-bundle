<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Extension\DataTableTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;

class DataTableRegistry implements DataTableRegistryInterface
{
    /**
     * @var array<string, ResolvedDataTableTypeInterface>
     */
    private array $types = [];

    /**
     * @var array<string, bool>
     */
    private array $checkedTypes = [];

    /**
     * @var array<string, DataTableTypeExtensionInterface>
     */
    private array $typeExtensions = [];

    /**
     * @param iterable<DataTableExtensionInterface> $extensions
     */
    public function __construct(
        private readonly iterable $extensions,
        private readonly ResolvedDataTableTypeFactoryInterface $resolvedDataTableTypeFactory,
    ) {
        foreach ($extensions as $extension) {
            if (!$extension instanceof DataTableExtensionInterface) {
                throw new UnexpectedTypeException($extension, DataTableExtensionInterface::class);
            }
        }
    }

    public function getType(string $name): ResolvedDataTableTypeInterface
    {
        if (!isset($this->types[$name])) {
            $type = null;

            foreach ($this->extensions as $extension) {
                if ($extension->hasType($name)) {
                    $type = $extension->getType($name);
                    break;
                }
            }

            if (!$type) {
                // Support fully-qualified class names
                if (!class_exists($name)) {
                    throw new InvalidArgumentException(sprintf('Could not load type "%s": class does not exist.', $name));
                }
                if (!is_subclass_of($name, DataTableTypeInterface::class)) {
                    throw new InvalidArgumentException(sprintf('Could not load type "%s": class does not implement "%s".', $name, DataTableTypeInterface::class));
                }

                $type = new $name();
            }

            $this->types[$name] = $this->resolveType($type);
        }

        return $this->types[$name];
    }

    private function resolveType(DataTableTypeInterface $type): ResolvedDataTableTypeInterface
    {
        $fqcn = $type::class;

        if (isset($this->checkedTypes[$fqcn])) {
            $types = implode(' > ', array_merge(array_keys($this->checkedTypes), [$fqcn]));
            throw new \LogicException(sprintf('Circular reference detected for data table type "%s" (%s).', $fqcn, $types));
        }

        $this->checkedTypes[$fqcn] = true;

        $typeExtensions = array_filter(
            $this->typeExtensions,
            fn (DataTableTypeExtensionInterface $extension) => $this->isFqcnExtensionEligible($fqcn, $extension),
        );

        $parentType = $type->getParent();

        try {
            return $this->resolvedDataTableTypeFactory->createResolvedType(
                $type,
                $typeExtensions,
                $parentType ? $this->getType($parentType) : null,
            );
        } finally {
            unset($this->checkedTypes[$fqcn]);
        }
    }

    private function isFqcnExtensionEligible(string $fqcn, DataTableTypeExtensionInterface $extension): bool
    {
        $extendedTypes = $extension::getExtendedTypes();

        if ($extendedTypes instanceof \Traversable) {
            $extendedTypes = iterator_to_array($extendedTypes);
        }

        return in_array($fqcn, $extendedTypes);
    }
}
