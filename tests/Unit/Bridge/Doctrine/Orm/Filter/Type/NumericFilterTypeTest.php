<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class NumericFilterTypeTest extends DoctrineOrmFilterTypeTestCase
{
    protected function getTestedType(): string
    {
        return NumericFilterType::class;
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

    protected function getDefaultFormType(): string
    {
        return NumberType::class;
    }
}
