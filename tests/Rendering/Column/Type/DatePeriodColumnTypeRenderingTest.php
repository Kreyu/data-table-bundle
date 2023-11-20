<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Rendering\Column\Type;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeZone;
use Kreyu\Bundle\DataTableBundle\Column\Type\DatePeriodColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeRenderingTestCase;

class DatePeriodColumnTypeRenderingTest extends ColumnTypeRenderingTestCase
{
    protected function getTestedType(): string
    {
        return DatePeriodColumnType::class;
    }

    public static function columnHeaderProvider(): iterable
    {
        yield from ColumnTypeRenderingTest::columnHeaderProvider();
    }

    public static function columnValueProvider(): iterable
    {
        $datePeriod = new DatePeriod(
            DateTime::createFromFormat(
                format: 'Y-m-d H:i:s',
                datetime: '2023-01-01 00:00:00',
                timezone: new DateTimeZone(static::DEFAULT_TIMEZONE),
            ),
            new DateInterval('P7D'),
            DateTime::createFromFormat(
                format: 'Y-m-d H:i:s',
                datetime: '2023-12-31 23:59:59',
                timezone: new DateTimeZone(static::DEFAULT_TIMEZONE),
            ),
        );

        foreach (static::THEMES as $themeName => $theme) {
            yield "default format with $themeName theme" => [
                'theme' => $theme,
                'column' => ['data' => $datePeriod],
                'expectedHtml' => '<span>01.01.2023 00:00:00 - 31.12.2023 23:59:59</span>',
            ];

            yield "custom format with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'data' => $datePeriod,
                    'options' => [
                        'format' => 'Y-m-d (H:i:s)',
                    ],
                ],
                'expectedHtml' => '<span>2023-01-01 (00:00:00) - 2023-12-31 (23:59:59)</span>',
            ];

            yield "custom timezone with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'data' => $datePeriod,
                    'options' => [
                        'timezone' => 'America/Los_Angeles',
                    ],
                ],
                'expectedHtml' => '<span>31.12.2022 15:00:00 - 31.12.2023 14:59:59</span>',
            ];

            yield "custom separator with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'data' => $datePeriod,
                    'options' => [
                        'separator' => ' to ',
                    ],
                ],
                'expectedHtml' => '<span>01.01.2023 00:00:00 to 31.12.2023 23:59:59</span>',
            ];
        }
    }
}
