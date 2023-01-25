<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension\Core;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Extension\AbstractTypeExtension;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class ColumnFactoryExtension extends AbstractTypeExtension
{
    public function __construct(
        private ColumnFactoryInterface $columnFactory,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->setColumnFactory($this->columnFactory);
    }

    public static function getExtendedTypes(): iterable
    {
        return [DataTableType::class];
    }
}