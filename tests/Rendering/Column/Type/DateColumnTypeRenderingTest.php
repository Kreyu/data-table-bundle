<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Rendering\Column\Type;

use DateTime;
use DateTimeZone;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeRenderingTestCase;

class DateColumnTypeRenderingTest extends ColumnTypeRenderingTestCase
{
    protected function getTestedType(): string
    {
        return DateColumnType::class;
    }

    public static function columnHeaderProvider(): iterable
    {
        yield from ColumnTypeRenderingTest::columnHeaderProvider();
    }

    public static function columnValueProvider(): iterable
    {
        $date = DateTime::createFromFormat(
            format: 'Y-m-d H:i:s',
            datetime: '2023-01-01 00:00:00',
            timezone: new DateTimeZone(static::DEFAULT_TIMEZONE),
        );

        foreach (static::THEMES as $themeName => $theme) {
            yield "default format with $themeName theme" => [
                'theme' => $theme,
                'column' => ['data' => $date],
                'expectedHtml' => '<span>01.01.2023</span>',
            ];

            yield "custom timezone with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'data' => $date,
                    'options' => [
                        'timezone' => 'America/Los_Angeles',
                    ],
                ],
                'expectedHtml' => '<span>31.12.2022</span>',
            ];

            yield "custom format with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'data' => $date,
                    'options' => [
                        'format' => 'Y.m.d',
                    ],
                ],
                'expectedHtml' => '<span>2023.01.01</span>',
            ];
        }
    }
}
