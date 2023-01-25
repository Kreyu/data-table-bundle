<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class DataTableFactory implements DataTableFactoryInterface
{
    public function __construct(
        private DataTableRegistryInterface $registry,
    ) {
    }

    public function create(string $type = DataTableType::class, ?ProxyQueryInterface $query = null, array $options = []): DataTableInterface
    {
        return $this->createBuilder($type, $query, $options)->getDataTable();
    }

    public function createNamed(string $name, string $type = DataTableType::class, ?ProxyQueryInterface $query = null, array $options = []): DataTableInterface
    {
        return $this->createNamedBuilder($name, $type, $query, $options)->getDataTable();
    }

    public function createBuilder(string $type = DataTableType::class, ?ProxyQueryInterface $query = null, array $options = []): DataTableBuilderInterface
    {
        return $this->createNamedBuilder($this->registry->getType($type)->getName(), $type, $query, $options);
    }

    public function createNamedBuilder(string $name, string $type = DataTableType::class, ?ProxyQueryInterface $query = null, array $options = []): DataTableBuilderInterface
    {
        $type = $this->registry->getType($type);

        $builder = $type->createBuilder($this, $name, $query, $options);

        $type->buildDataTable($builder, $builder->getOptions());

        return $builder;
    }
}
