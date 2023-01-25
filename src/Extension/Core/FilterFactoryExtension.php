<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension\Core;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Extension\AbstractTypeExtension;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class FilterFactoryExtension extends AbstractTypeExtension
{
    public function __construct(
        private FilterFactoryInterface $filterFactory,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->setFilterFactory($this->filterFactory);
    }

    public static function getExtendedTypes(): iterable
    {
        return [DataTableType::class];
    }
}
