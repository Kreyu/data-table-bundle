<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Mapper\Factory;

use Kreyu\Bundle\DataTableBundle\Filter\Factory\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Mapper\FilterMapper;
use Kreyu\Bundle\DataTableBundle\Filter\Mapper\FilterMapperInterface;

class FilterMapperFactory implements FilterMapperFactoryInterface
{
    public function __construct(
        private readonly FilterFactoryInterface $filterFactory,
    ) {
    }

    public function create(): FilterMapperInterface
    {
        return new FilterMapper($this->filterFactory);
    }
}
