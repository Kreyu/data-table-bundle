<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Factory;

use Kreyu\Bundle\DataTableBundle\Column\Mapper\Factory\ColumnMapperFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTable;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Mapper\Factory\FilterMapperFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Query\Factory\ProxyQueryFactoryChainInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeChainInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class DataTableFactory implements DataTableFactoryInterface
{
    public function __construct(
        private readonly DataTableTypeChainInterface     $dataTableTypeChain,
        private readonly ProxyQueryFactoryChainInterface $proxyQueryFactoryChain,
        private readonly ColumnMapperFactoryInterface    $columnMapperFactory,
        private readonly FilterMapperFactoryInterface    $filterMapperFactory,
    ) {
    }

    public function create(string $typeClass, mixed $data, array $options = []): DataTableInterface
    {
        if (null === $type = $this->dataTableTypeChain->get($typeClass)) {
            throw new InvalidArgumentException(sprintf('Data table type "%s" not found in the chain', $typeClass));
        }

        $proxyQueryFactory = $this->proxyQueryFactoryChain->getDataCompatibleFactory($data);
        $proxyQuery = $proxyQueryFactory->create($data);

        $columnMapper = $this->columnMapperFactory->create();
        $filterMapper = $this->filterMapperFactory->create();

        $type->configureColumns($columnMapper);
        $type->configureFilters($filterMapper);

        return new DataTable(
            query: $proxyQuery,
            columns: $columnMapper->all(),
            filters: $filterMapper->all(),
        );
    }
}
