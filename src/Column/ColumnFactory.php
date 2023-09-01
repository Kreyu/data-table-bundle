<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;

class ColumnFactory implements ColumnFactoryInterface
{
    public function __construct(
        private readonly ColumnRegistryInterface $registry,
    ) {
    }

    public function create(string $type = ColumnType::class, array $options = []): ColumnInterface
    {
        return $this->createBuilder($type, $options)->getColumn();
    }

    public function createNamed(string $name, string $type = ColumnType::class, array $options = []): ColumnInterface
    {
        return $this->createNamedBuilder($name, $type, $options)->getColumn();
    }

    public function createBuilder(string $type = ColumnType::class, array $options = []): ColumnBuilderInterface
    {
        return $this->createNamedBuilder($this->registry->getType($type)->getBlockPrefix(), $type, $options);
    }

    public function createNamedBuilder(string $name, string $type = ColumnType::class, array $options = []): ColumnBuilderInterface
    {
        $type = $this->registry->getType($type);

        $builder = $type->createBuilder($this, $name, $options);

        $type->buildColumn($builder, $builder->getOptions());

        return $builder;
    }
}
