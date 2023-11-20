<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Rendering\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeRenderingTestCase;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\StringableValue;

class NumberColumnTypeRenderingTest extends ColumnTypeRenderingTestCase
{
    protected function getTestedType(): string
    {
        return NumberColumnType::class;
    }

    public static function columnHeaderProvider(): iterable
    {
        yield from ColumnTypeRenderingTest::columnHeaderProvider();
    }

    public static function columnValueProvider(): iterable
    {
        foreach (static::THEMES as $themeName => $theme) {
            yield "numeric value with $themeName theme" => [
                'theme' => $theme,
                'column' => ['data' => 1000],
                'expectedHtml' => match ($theme) {
                    static::THEME_BASE => '<span>1000</span>',
                    static::THEME_BOOTSTRAP, static::THEME_TABLER => <<<HTML
                        <div class="text-end">
                            <span>1000</span>
                        </div>
                    HTML,
                },
            ];

            yield "stringable value with $theme theme" => [
                'theme' => $theme,
                'column' => ['data' => new StringableValue(1000)],
                'expectedHtml' => match ($theme) {
                    static::THEME_BASE => '<span>1000</span>',
                    static::THEME_BOOTSTRAP, static::THEME_TABLER => <<<HTML
                        <div class="text-end">
                            <span>1000</span>
                        </div>
                    HTML,
                },
            ];
        }
    }
}
