<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\EventListener;

use Doctrine\ORM\Query\Expr;
use Kreyu\Bundle\DataTableBundle\Filter\FilterConfigInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\PreApplyExpressionEvent;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\EventListener\ApplyExpressionTransformers;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\Filter\ExpressionTransformer\CustomExpressionTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApplyExpressionTransformersTest extends TestCase
{
    private ApplyExpressionTransformers $listener;

    protected function setUp(): void
    {
        $this->listener = new ApplyExpressionTransformers();
    }

    #[DataProvider('providePreApplyExpressionCases')]
    public function testPreApplyExpression(array $options, string $expected)
    {
        $expression = new Expr\Comparison('foo', '=', 'bar');

        $event = new PreApplyExpressionEvent(
            $this->createMock(DoctrineOrmProxyQueryInterface::class),
            $this->createMock(FilterData::class),
            $this->createFilterMock($options),
            $expression,
        );

        $this->listener->preApplyExpression($event);

        $this->assertEquals($expected, $event->getExpression());
    }

    public static function providePreApplyExpressionCases(): iterable
    {
        yield 'Using "trim" option' => [
            ['trim' => true],
            'TRIM(foo) = TRIM(bar)',
        ];

        yield 'Using "lower" option' => [
            ['lower' => true],
            'LOWER(foo) = LOWER(bar)',
        ];

        yield 'Using "upper" option' => [
            ['upper' => true],
            'UPPER(foo) = UPPER(bar)',
        ];

        yield 'Using "expression_transformers" option' => [
            ['expression_transformers' => [new CustomExpressionTransformer()]],
            'CUSTOM(foo) = CUSTOM(bar)',
        ];

        yield 'Using "trim", "lower" and "upper" options with "expression_transformers" option' => [
            ['trim' => true, 'lower' => true, 'upper' => true, 'expression_transformers' => [new CustomExpressionTransformer()]],
            'CUSTOM(UPPER(LOWER(TRIM(foo)))) = CUSTOM(UPPER(LOWER(TRIM(bar))))',
        ];
    }

    private function createFilterMock(array $options = []): FilterInterface&MockObject
    {
        $options += [
            'trim' => false,
            'lower' => false,
            'upper' => false,
            'expression_transformers' => [],
        ];

        $filterConfig = $this->createMock(FilterConfigInterface::class);
        $filterConfig->method('getOption')->willReturnCallback(function (string $option) use ($options) {
            return $options[$option] ?? null;
        });

        $filter = $this->createMock(FilterInterface::class);
        $filter->method('getConfig')->willReturn($filterConfig);

        return $filter;
    }
}
