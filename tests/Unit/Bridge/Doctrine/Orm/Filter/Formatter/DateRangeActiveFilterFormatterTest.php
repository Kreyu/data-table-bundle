<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\Formatter;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Formatter\DateRangeActiveFilterFormatter;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatableMessage;

class DateRangeActiveFilterFormatterTest extends TestCase
{
    private DateRangeActiveFilterFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new DateRangeActiveFilterFormatter();
    }

    public function testDateRangeWithFromDateOnly()
    {
        $filterData = new FilterData(['from' => $dateFrom = new \DateTime('2023-01-01'), 'to' => null]);

        $result = ($this->formatter)($filterData);

        $this->assertEquals(
            new TranslatableMessage('After %date%', ['%date%' => $dateFrom->format('Y-m-d')], 'KreyuDataTable'),
            $result,
        );
    }

    public function testDateRangeWithToDateOnly()
    {
        $filterData = new FilterData(['from' => null, 'to' => $dateTo = new \DateTime('2023-01-01')]);

        $result = ($this->formatter)($filterData);

        $this->assertEquals(
            new TranslatableMessage('Before %date%', ['%date%' => $dateTo->format('Y-m-d')], 'KreyuDataTable'),
            $result,
        );
    }

    public function testDateRangeWithEqualDates()
    {
        $date = new \DateTime('2023-01-20');
        $filterData = new FilterData(['from' => $date, 'to' => $date]);

        $result = ($this->formatter)($filterData);

        $this->assertEquals('2023-01-20', $result);
    }

    public function testDateRangeWithBothDates()
    {
        $dateFrom = new \DateTime('2023-01-01');
        $dateTo = new \DateTime('2023-01-15');
        $filterData = new FilterData(['from' => $dateFrom, 'to' => $dateTo]);

        $result = ($this->formatter)($filterData);

        $this->assertEquals('2023-01-01 - 2023-01-15', $result);
    }
}
