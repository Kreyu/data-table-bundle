<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Rendering\Column\Type;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeRenderingTestCase;

class DateTimeColumnTypeRenderingTest extends ColumnTypeRenderingTestCase
{
    protected function getTestedType(): string
    {
        return DateTimeColumnType::class;
    }

    public static function columnHeaderProvider(): iterable
    {
        yield from ColumnTypeRenderingTest::columnHeaderProvider();
    }

    public static function columnValueProvider(): iterable
    {
        $dateTime = DateTimeImmutable::createFromFormat(
            format: 'Y-m-d H:i:s',
            datetime: '2023-01-01 00:15:30',
            timezone: new DateTimeZone(static::DEFAULT_TIMEZONE),
        );

        foreach (static::THEMES as $themeName => $theme) {
            yield "default format with $themeName theme" => [
                'theme' => $theme,
                'column' => ['data' => $dateTime],
                'expectedHtml' => '<span>01.01.2023 00:15:30</span>',
            ];

            yield "custom timezone with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'data' => $dateTime,
                    'options' => [
                        'timezone' => 'America/Los_Angeles',
                    ],
                ],
                'expectedHtml' => '<span>31.12.2022 15:15:30</span>',
            ];

            yield "custom format with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'data' => $dateTime,
                    'options' => [
                        'format' => 'Y.m.d (H:i:s)',
                    ],
                ],
                'expectedHtml' => '<span>2023.01.01 (00:15:30)</span>',
            ];
        }
    }
}
