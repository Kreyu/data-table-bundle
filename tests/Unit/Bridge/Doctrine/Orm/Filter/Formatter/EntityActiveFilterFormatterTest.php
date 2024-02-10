<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\Formatter;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Formatter\EntityActiveFilterFormatter;
use Kreyu\Bundle\DataTableBundle\Filter\FilterConfigInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use PHPUnit\Framework\TestCase;

class EntityActiveFilterFormatterTest extends TestCase
{
    public function testItCastsFilterDataValueToString()
    {
        $this->assertEquals('123', $this->format(123));
    }

    public function testItUsesChoiceLabelOptionAsPropertyPath()
    {
        $this->assertEquals('bar', $this->format(['foo' => 'bar'], ['choice_label' => '[foo]']));
    }

    public function testItUsesChoiceLabelOptionCallable()
    {
        $this->assertEquals('bar', $this->format('foo', ['choice_label' => fn () => 'bar']));
    }

    private function format(mixed $value, array $filterOptions = []): string
    {
        $formatter = new EntityActiveFilterFormatter();
        $data = new FilterData($value);

        $filter = $this->createMock(FilterInterface::class);
        $filterConfig = $this->createMock(FilterConfigInterface::class);

        $filterConfig->method('getOption')->willReturnCallback(function (string $name) use ($filterOptions) {
            return $filterOptions[$name] ?? null;
        });

        $filter->method('getConfig')->willReturn($filterConfig);

        return ($formatter)($data, $filter);
    }
}
