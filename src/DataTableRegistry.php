<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Extension\DataTableTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;

class DataTableRegistry implements DataTableRegistryInterface
{
    /**
     * @var array<DataTableTypeInterface>
     */
    private array $types;

    /**
     * @var array<DataTableTypeExtensionInterface>
     */
    private array $typeExtensions;

    /**
     * @var array<ProxyQueryFactoryInterface>
     */
    private array $proxyQueryFactories;

    /**
     * @var array<ResolvedDataTableTypeInterface>
     */
    private array $resolvedTypes;

    /**
     * @var array<class-string<DataTableTypeInterface>, bool>
     */
    private array $checkedTypes;

    /**
     * @param iterable<DataTableTypeInterface>          $types
     * @param iterable<DataTableTypeExtensionInterface> $typeExtensions
     * @param iterable<ProxyQueryInterface>             $proxyQueryFactories
     */
    public function __construct(
        iterable $types,
        iterable $typeExtensions,
        iterable $proxyQueryFactories,
        private readonly ResolvedDataTableTypeFactoryInterface $resolvedTypeFactory,
    ) {
        $this->setTypes($types);
        $this->setTypeExtensions($typeExtensions);
        $this->setProxyQueryFactories($proxyQueryFactories);
    }

    public function getType(string $name): ResolvedDataTableTypeInterface
    {
        return $this->resolvedTypes[$name] ??= $this->resolveType($name);
    }

    public function hasType(string $name): bool
    {
        return isset($this->types[$name]);
    }

    public function getProxyQueryFactories(): array
    {
        return $this->proxyQueryFactories;
    }

    private function resolveType(string $name): ResolvedDataTableTypeInterface
    {
        $type = $this->types[$name] ?? throw new InvalidArgumentException(sprintf('The data table type %s does not exist', $name));

        if (isset($this->checkedTypes[$fqcn = $type::class])) {
            $types = implode(' > ', array_merge(array_keys($this->checkedTypes), [$fqcn]));
            throw new LogicException(sprintf('Circular reference detected for data table type "%s" (%s).', $fqcn, $types));
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
            if (!$type instanceof DataTableTypeInterface) {
                throw new UnexpectedTypeException($type, DataTableTypeInterface::class);
            }

            $this->types[$type::class] = $type;
        }
    }

    private function setTypeExtensions(iterable $typeExtensions): void
    {
        $this->typeExtensions = [];

        foreach ($typeExtensions as $typeExtension) {
            if (!$typeExtension instanceof DataTableTypeExtensionInterface) {
                throw new UnexpectedTypeException($typeExtension, DataTableTypeExtensionInterface::class);
            }

            foreach ($typeExtension::getExtendedTypes() as $extendedType) {
                $this->typeExtensions[$extendedType][] = $typeExtension;
            }
        }
    }

    private function setProxyQueryFactories(iterable $proxyQueryFactories): void
    {
        $this->proxyQueryFactories = [];

        foreach ($proxyQueryFactories as $proxyQueryFactory) {
            if (!$proxyQueryFactory instanceof ProxyQueryFactoryInterface) {
                throw new UnexpectedTypeException($proxyQueryFactory, ProxyQueryFactoryInterface::class);
            }

            $this->proxyQueryFactories[] = $proxyQueryFactory;
        }
    }
}
