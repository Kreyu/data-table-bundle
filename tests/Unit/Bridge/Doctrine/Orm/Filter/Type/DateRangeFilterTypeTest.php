<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\DateRangeType;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateRangeFilterType;

class DateRangeFilterTypeTest extends DoctrineOrmFilterTypeTestCase
{
    protected function getTestedType(): string
    {
        return DateRangeFilterType::class;
    }

    protected function getDefaultOperator(): Operator
    {
        return Operator::Between;
    }

    protected function getSupportedOperators(): array
    {
        return [
            Operator::Between,
        ];
    }

    protected function getDefaultFormType(): string
    {
        return DateRangeType::class;
    }
}
