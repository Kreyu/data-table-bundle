<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterType;

class FilterFactory implements FilterFactoryInterface
{
    public function __construct(
        private readonly FilterRegistryInterface $registry,
    ) {
    }

    public function create(string $type = FilterType::class, array $options = []): FilterInterface
    {
        return $this->createBuilder($type, $options)->getFilter();
    }

    public function createNamed(string $name, string $type = FilterType::class, array $options = []): FilterInterface
    {
        return $this->createNamedBuilder($name, $type, $options)->getFilter();
    }

    public function createBuilder(string $type = FilterType::class, array $options = []): FilterBuilderInterface
    {
        return $this->createNamedBuilder($this->registry->getType($type)->getBlockPrefix(), $type, $options);
    }

    public function createNamedBuilder(string $name, string $type = FilterType::class, array $options = []): FilterBuilderInterface
    {
        $type = $this->registry->getType($type);

        $builder = $type->createBuilder($this, $name, $options);

        $type->buildFilter($builder, $builder->getOptions());

        return $builder;
    }
}
