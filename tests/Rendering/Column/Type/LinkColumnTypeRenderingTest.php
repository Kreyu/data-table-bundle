<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Rendering\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\LinkColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeRenderingTestCase;

class LinkColumnTypeRenderingTest extends ColumnTypeRenderingTestCase
{
    protected function getTestedType(): string
    {
        return LinkColumnType::class;
    }

    public static function columnHeaderProvider(): iterable
    {
        yield from ColumnTypeRenderingTest::columnHeaderProvider();
    }

    public static function columnValueProvider(): iterable
    {
        foreach (static::THEMES as $themeName => $theme) {
            yield "default href with $themeName theme" => [
                'theme' => $theme,
                'expectedHtml' => <<<HTML
                    <a href="#" target="_self">
                        <span></span>
                    </a>
                HTML,
            ];

            yield "custom href with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'options' => [
                        'href' => '/products',
                    ],
                ],
                'expectedHtml' => <<<HTML
                    <a href="/products" target="_self">
                        <span></span>
                    </a>
                HTML,
            ];

            yield "callable href with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'data' => 'Example product',
                    'options' => [
                        'href' => fn (string $slug) => '/products/'.strtolower(str_replace(' ', '-', $slug)),
                    ],
                ],
                'expectedHtml' => <<<HTML
                    <a href="/products/example-product" target="_self">
                        <span>Example product</span>
                    </a>
                HTML,
            ];

            yield "custom target with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'options' => [
                        'target' => '_blank',
                    ],
                ],
                'expectedHtml' => <<<HTML
                    <a href="#" target="_blank">
                        <span></span>
                    </a>
                HTML,
            ];

            yield "callable callable href with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'options' => [
                        'target' => fn () => '_blank',
                    ],
                ],
                'expectedHtml' => <<<HTML
                    <a href="#" target="_blank">
                        <span></span>
                    </a>
                HTML,
            ];
        }
    }
}
