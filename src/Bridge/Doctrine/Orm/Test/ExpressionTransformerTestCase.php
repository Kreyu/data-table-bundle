<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Test;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\ExpressionTransformerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

abstract class ExpressionTransformerTestCase extends TestCase
{
    abstract public static function createTransformer(): ExpressionTransformerInterface;

    abstract public static function expressionTransformationProvider(): iterable;

    #[DataProvider('expressionTransformationProvider')]
    public function testExpressionTransformation(ExpressionTransformerInterface $expressionTransformer, mixed $inputExpression, mixed $outputExpression): void
    {
        $this->assertEquals($outputExpression, $expressionTransformer->transform($inputExpression));
    }
}
