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

    private string $previousLocale;

    protected function setUp(): void
    {
        $this->formatter = new DateRangeActiveFilterFormatter();

        $isIntlLoaded = extension_loaded('intl');

        if (!$isIntlLoaded) {
            return;
        }

        $this->previousLocale = \Locale::getDefault();

        \Locale::setDefault('en');
    }

    protected function tearDown(): void
    {
        $isIntlLoaded = extension_loaded('intl');

        if (!$isIntlLoaded) {
            return;
        }

        \Locale::setDefault($this->previousLocale);
    }

    public function testDateRangeWithFromDateOnly()
    {
        $filterData = new FilterData(['from' => new \DateTime('2023-01-01'), 'to' => null]);
        $expectedDate = extension_loaded('intl') ? 'Jan 1, 2023' : '2023-01-01';

        $result = ($this->formatter)($filterData);

        $this->assertEquals(
            new TranslatableMessage('After %date%', ['%date%' => $expectedDate], 'KreyuDataTable'),
            $result,
        );
    }

    public function testDateRangeWithToDateOnly()
    {
        $filterData = new FilterData(['from' => null, 'to' => new \DateTime('2023-01-01')]);
        $expectedDate = extension_loaded('intl') ? 'Jan 1, 2023' : '2023-01-01';

        $result = ($this->formatter)($filterData);

        $this->assertEquals(
            new TranslatableMessage('Before %date%', ['%date%' => $expectedDate], 'KreyuDataTable'),
            $result,
        );
    }

    public function testDateRangeWithEqualDates()
    {
        $date = new \DateTime('2023-01-20');
        $filterData = new FilterData(['from' => $date, 'to' => $date]);
        $expected = extension_loaded('intl') ? 'Jan 20, 2023' : '2023-01-20';

        $result = ($this->formatter)($filterData);

        $this->assertSame($expected, $result);
    }

    public function testDateRangeWithBothDates()
    {
        $dateFrom = new \DateTime('2023-01-01');
        $dateTo = new \DateTime('2023-01-15');
        $filterData = new FilterData(['from' => $dateFrom, 'to' => $dateTo]);
        $expected = extension_loaded('intl') ? 'Jan 1, 2023 - Jan 15, 2023' : '2023-01-01 - 2023-01-15';

        $result = ($this->formatter)($filterData);

        $this->assertSame($expected, $result);
    }
}
