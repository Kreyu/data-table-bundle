<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;

class StringFilterTypeTest extends DoctrineOrmFilterTypeTestCase
{
    protected function getTestedType(): string
    {
        return StringFilterType::class;
    }

    protected function getDefaultOperator(): Operator
    {
        return Operator::Contains;
    }

    protected function getSupportedOperators(): array
    {
        return [
            Operator::Equals,
            Operator::NotEquals,
            Operator::Contains,
            Operator::NotContains,
            Operator::StartsWith,
            Operator::EndsWith,
        ];
    }
}
