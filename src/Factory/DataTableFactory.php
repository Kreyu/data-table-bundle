<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Factory;

use Kreyu\Bundle\DataTableBundle\Column\Mapper\Factory\ColumnMapperFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTable;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Mapper\Factory\FilterMapperFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeChainInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataTableFactory implements DataTableFactoryInterface
{
    public function __construct(
        private readonly DataTableTypeChainInterface $dataTableTypeChain,
        private readonly ColumnMapperFactoryInterface $columnMapperFactory,
        private readonly FilterMapperFactoryInterface $filterMapperFactory,
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    public function create(string $typeClass, array $options = []): DataTableInterface
    {
        if (null === $type = $this->dataTableTypeChain->get($typeClass)) {
            throw new InvalidArgumentException(sprintf('Data table type "%s" not found in the chain', $typeClass));
        }

        return $this->doCreate($type->getName(), $type, $options);
    }

    public function createNamed(string $name, string $typeClass, array $options = []): DataTableInterface
    {
        if (null === $type = $this->dataTableTypeChain->get($typeClass)) {
            throw new InvalidArgumentException(sprintf('Data table type "%s" not found in the chain', $typeClass));
        }

        return $this->doCreate($name, $type, $options);
    }

    private function doCreate(string $name, DataTableTypeInterface $type, array $options): DataTableInterface
    {
        $query = $type->createQuery();

        $optionsResolver = new OptionsResolver();

        $type->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve($options);

        $columnMapper = $this->columnMapperFactory->create();
        $filterMapper = $this->filterMapperFactory->create();

        $type->configureColumns($columnMapper, $options);
        $type->configureFilters($filterMapper, $options);

        $personalizationData = null;

        if ($type->hasPersonalization()) {
            $personalizationData = new PersonalizationData($columnMapper->all());

            $type->configurePersonalizationData($personalizationData, $options);
        }

        return new DataTable(
            name: $name,
            query: $query,
            columns: $columnMapper->all(),
            filters: $filterMapper->all(),
            formFactory: $this->formFactory,
            filterPersister: $type->getFilterPersister(),
            filterPersisterSubject: $type->getFilterPersisterSubject(),
            personalizationPersister: $type->getPersonalizationPersister(),
            personalizationPersisterSubject: $type->getPersonalizationPersisterSubject(),
            personalizationData: $personalizationData,
        );
    }
}
