<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Rendering\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\BooleanColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeRenderingTestCase;

class BooleanColumnTypeRenderingTest extends ColumnTypeRenderingTestCase
{
    protected function getTestedType(): string
    {
        return BooleanColumnType::class;
    }

    public static function columnHeaderProvider(): iterable
    {
        yield from ColumnTypeRenderingTest::columnHeaderProvider();
    }

    public static function columnValueProvider(): iterable
    {
        foreach (static::THEMES as $themeName => $theme) {
            yield "truthy value with $themeName theme" => [
                'theme' => $theme,
                'column' => ['data' => true],
                'expectedHtml' => match ($theme) {
                    static::THEME_BASE => '<span>Yes</span>',
                    static::THEME_BOOTSTRAP => '<span class="badge bg-success">Yes</span>',
                    static::THEME_TABLER => '<span class="badge bg-green-lt">Yes</span>',
                },
            ];

            yield "falsy value with $themeName theme" => [
                'theme' => $theme,
                'column' => ['data' => false],
                'expectedHtml' => match ($theme) {
                    static::THEME_BASE => '<span>No</span>',
                    static::THEME_BOOTSTRAP => '<span class="badge bg-danger">No</span>',
                    static::THEME_TABLER => '<span class="badge bg-red-lt">No</span>',
                },
            ];
        }
    }
}
