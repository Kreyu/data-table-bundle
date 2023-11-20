<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Rendering\Column\Type;

use ArrayIterator;
use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeRenderingTestCase;

class CollectionColumnTypeRenderingTest extends ColumnTypeRenderingTestCase
{
    protected function getTestedType(): string
    {
        return CollectionColumnType::class;
    }

    public static function columnHeaderProvider(): iterable
    {
        yield from ColumnTypeRenderingTest::columnHeaderProvider();
    }

    public static function columnValueProvider(): iterable
    {
        foreach (static::THEMES as $themeName => $theme) {
            yield "empty collection with $themeName theme" => [
                'theme' => $theme,
                'column' => ['data' => []],
                'expectedHtml' => '',
            ];

            yield "single item collection with $themeName theme" => [
                'theme' => $theme,
                'column' => ['data' => ['foo']],
                'expectedHtml' => '<span>foo</span>',
            ];

            yield "multiple item collection with $themeName theme" => [
                'theme' => $theme,
                'column' => ['data' => ['foo', 'bar']],
                'expectedHtml' => '<span>foo</span><span>, </span><span>bar</span>',
            ];

            yield "multiple item collection with custom separator with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'data' => ['foo', 'bar'],
                    'options' => [
                        'separator' => ' - ',
                    ],
                ],
                'expectedHtml' => '<span>foo</span><span> - </span><span>bar</span>',
            ];

            yield "multiple item collection with iterable object collection with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'data' => new ArrayIterator(['foo', 'bar']),
                ],
                'expectedHtml' => '<span>foo</span><span>, </span><span>bar</span>',
            ];
        }
    }
}
