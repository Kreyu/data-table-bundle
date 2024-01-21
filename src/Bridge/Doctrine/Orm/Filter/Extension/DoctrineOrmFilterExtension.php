<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Extension;

use Kreyu\Bundle\DataTableBundle\Filter\Extension\AbstractFilterExtension;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\BooleanFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateRangeFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateTimeFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DoctrineOrmFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\EntityFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;

class DoctrineOrmFilterExtension extends AbstractFilterExtension
{
    protected function loadTypes(): array
    {
        return [
            new DoctrineOrmFilterType(),
            new StringFilterType(),
            new NumericFilterType(),
            new BooleanFilterType(),
            new DateFilterType(),
            new DateTimeFilterType(),
            new DateRangeFilterType(),
            new EntityFilterType(),
        ];
    }
}
