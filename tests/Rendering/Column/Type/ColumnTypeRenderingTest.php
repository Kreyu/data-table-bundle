<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Rendering\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeRenderingTestCase;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\StringableValue;

class ColumnTypeRenderingTest extends ColumnTypeRenderingTestCase
{
    protected function getTestedType(): string
    {
        return ColumnType::class;
    }

    public static function columnHeaderProvider(): iterable
    {
        foreach (static::THEMES as $themeName => $theme) {
            yield "with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'options' => [
                        'label' => 'Foo Bar',
                    ],
                ],
                'expectedHtml' => match ($theme) {
                    static::THEME_BASE => <<<HTML
                        <th>
                            <span>
                                <span>Foo Bar</span>
                            </span>
                        </th>
                    HTML,
                    static::THEME_BOOTSTRAP, static::THEME_TABLER => <<<HTML
                        <th>
                            <span class="text-decoration-none text-reset d-block w-100 h-100 py-1">
                                <span>Foo Bar</span>
                            </span>
                        </th>
                    HTML,
                },
            ];

            yield "sortable with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'options' => [
                        'label' => 'Foo Bar',
                        'sort' => true,
                    ],
                ],
                'expectedHtml' => match ($theme) {
                    static::THEME_BASE =>  <<<HTML
                        <th>
                            <a data-turbo-action="advance" href="">
                                <span>Foo Bar</span>
                            </a>
                        </th>
                    HTML,
                    static::THEME_BOOTSTRAP => <<<HTML
                        <th>
                            <a class="text-decoration-none text-reset d-block w-100 h-100 py-1" data-turbo-action="advance" href="">
                                <span>Foo Bar</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chevron-expand" fill="currentColor" height="16" viewBox="0 0 16 16" width="16">
                                    <path d="M3.646 9.146a.5.5 0 0 1 .708 0L8 12.793l3.646-3.647a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 0-.708zm0-2.292a.5.5 0 0 0 .708 0L8 3.207l3.646 3.647a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 0 0 0 .708z" fill-rule="evenodd"/>
                                </svg>
                            </a>
                        </th>
                    HTML,
                    static::THEME_TABLER => <<<HTML
                        <th>
                            <a class="text-decoration-none text-reset d-block w-100 h-100 py-1" data-turbo-action="advance" href="">
                                <span>Foo Bar</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm icon-thick" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24">
                                    <path d="M0 0h24v24H0z" fill="none" stroke="none"/>
                                    <path d="M8 9l4 -4l4 4"/>
                                    <path d="M16 15l-4 4l-4 -4"/>
                                </svg>
                            </a>
                        </th>
                    HTML,
                },
            ];
        }
    }

    public static function columnValueProvider(): iterable
    {
        foreach (static::THEMES as $themeName => $theme) {
            yield "no value with $themeName theme" => [
                'theme' => $theme,
                'expectedHtml' => '<span/>',
            ];

            yield "string value with $themeName theme" => [
                'theme' => $theme,
                'column' => ['data' => 'Foo bar'],
                'expectedHtml' => '<span>Foo bar</span>',
            ];

            yield "stringable value with $themeName theme" => [
                'theme' => $theme,
                'column' => ['data' => new StringableValue('Foo bar')],
                'expectedHtml' => '<span>Foo bar</span>',
            ];
        }
    }
}
