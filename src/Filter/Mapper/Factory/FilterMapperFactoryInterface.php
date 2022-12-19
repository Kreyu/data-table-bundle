<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Mapper\Factory;

use Kreyu\Bundle\DataTableBundle\Filter\Mapper\FilterMapperInterface;

interface FilterMapperFactoryInterface
{
    public function create(): FilterMapperInterface;
}
