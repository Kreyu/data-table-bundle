<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class DataTableFactory implements DataTableFactoryInterface
{
    public function __construct(
        private DataTableRegistryInterface $registry,
        private ?ProxyQueryFactoryInterface $proxyQueryFactory = null,
    ) {
    }

    public function create(string $type = DataTableType::class, mixed $query = null, array $options = []): DataTableInterface
    {
        return $this->createBuilder($type, $query, $options)->getDataTable();
    }

    public function createNamed(string $name, string $type = DataTableType::class, mixed $query = null, array $options = []): DataTableInterface
    {
        return $this->createNamedBuilder($name, $type, $query, $options)->getDataTable();
    }

    public function createBuilder(string $type = DataTableType::class, mixed $query = null, array $options = []): DataTableBuilderInterface
    {
        return $this->createNamedBuilder($this->registry->getType($type)->getName(), $type, $query, $options);
    }

    public function createNamedBuilder(string $name, string $type = DataTableType::class, mixed $query = null, array $options = []): DataTableBuilderInterface
    {
        if (null !== $query && !$query instanceof ProxyQueryInterface) {
            if (null === $this->proxyQueryFactory) {
                throw new InvalidArgumentException(sprintf('Expected query of type %s, %s given', ProxyQueryInterface::class, get_debug_type($query)));
            }

            $query = $this->proxyQueryFactory->create($query);
        }

        $type = $this->registry->getType($type);

        $builder = $type->createBuilder($this, $name, $query, $options);

        $type->buildDataTable($builder, $builder->getOptions());

        return $builder;
    }
}
