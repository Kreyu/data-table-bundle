<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\Formatter;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Formatter\DateActiveFilterFormatter;
use Kreyu\Bundle\DataTableBundle\Filter\FilterConfigInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use PHPUnit\Framework\TestCase;

class DateActiveFilterFormatterTest extends TestCase
{
    private string $previousLocale;

    protected function setUp(): void
    {
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

    public function testItCastsValueToStringIfNotDateTime()
    {
        $this->assertSame('123', $this->format(123));
    }

    public function testItFormatsDateTimeByLocaleIfInputFormatFormOptionIsNotGiven()
    {
        $expected = extension_loaded('intl') ? 'Jan 1, 2023' : '2023-01-01';

        $this->assertSame($expected, $this->format(new \DateTime('2023-01-01')));
    }

    public function testItFormatsDateTimeUsingInputFormatFormOption()
    {
        $this->assertSame('2023/01/01', $this->format(new \DateTime('2023-01-01'), [
            'form_options' => [
                'input_format' => 'Y/m/d',
            ],
        ]));
    }

    private function format(mixed $value, array $filterOptions = []): string
    {
        $formatter = new DateActiveFilterFormatter();
        $data = new FilterData($value);

        $filter = $this->createMock(FilterInterface::class);
        $filterConfig = $this->createMock(FilterConfigInterface::class);

        $filterConfig
            ->method('getOption')
            ->willReturnCallback(static function (string $name) use ($filterOptions) {
                return $filterOptions[$name] ?? null;
            })
        ;

        $filter->method('getConfig')->willReturn($filterConfig);

        return $formatter($data, $filter);
    }
}
