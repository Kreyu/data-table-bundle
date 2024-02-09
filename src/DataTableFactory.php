<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class DataTableFactory implements DataTableFactoryInterface
{
    public function __construct(
        private readonly DataTableRegistryInterface $registry,
    ) {
    }

    public function create(string $type = DataTableType::class, mixed $data = null, array $options = []): DataTableInterface
    {
        return $this->createBuilder($type, $data, $options)->getDataTable();
    }

    public function createNamed(string $name, string $type = DataTableType::class, mixed $data = null, array $options = []): DataTableInterface
    {
        return $this->createNamedBuilder($name, $type, $data, $options)->getDataTable();
    }

    public function createBuilder(string $type = DataTableType::class, mixed $data = null, array $options = []): DataTableBuilderInterface
    {
        return $this->createNamedBuilder($this->registry->getType($type)->getName(), $type, $data, $options);
    }

    public function createNamedBuilder(string $name, string $type = DataTableType::class, mixed $data = null, array $options = []): DataTableBuilderInterface
    {
        $query = $data;

        if (null !== $data && !$data instanceof ProxyQueryInterface) {
            foreach ($this->registry->getProxyQueryFactories() as $proxyQueryFactory) {
                if ($proxyQueryFactory->supports($data)) {
                    $query = $proxyQueryFactory->create($data);
                }

                break;
            }
        }

        $type = $this->registry->getType($type);

        $builder = $type->createBuilder($this, $name, $query, $options);

        $type->buildDataTable($builder, $builder->getOptions());

        return $builder;
    }
}
