<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\CallbackFilterType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Test\Filter\FilterTypeTestCase;

class CallbackFilterTypeTest extends FilterTypeTestCase
{
    private ?FilterInterface $expectedFilter = null;

    public function testItCallsGivenCallback(): void
    {
        $expectedQuery = $this->createMock(ProxyQueryInterface::class);
        $expectedData = $this->createMock(FilterData::class);

        $filter = $this->expectedFilter = $this->createFilter([
            'callback' => function (ProxyQueryInterface $query, FilterData $data, FilterInterface $filter) use ($expectedQuery, $expectedData) {
                $this->assertEquals($expectedQuery, $query);
                $this->assertEquals($expectedData, $data);
                $this->assertEquals($this->expectedFilter, $filter);
            },
        ]);

        $filter->handle($expectedQuery, $expectedData);
    }

    protected function getTestedType(): string
    {
        return CallbackFilterType::class;
    }
}
