<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Rendering\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\CheckboxColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeRenderingTestCase;

class CheckboxColumnTypeRenderingTest extends ColumnTypeRenderingTestCase
{
    protected function getTestedType(): string
    {
        return CheckboxColumnType::class;
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
                            <input 
                                aria-label="Select all checkbox" 
                                data-action="input-&gt;kreyu--data-table-bundle--batch#selectAll" 
                                data-identifier-name="id" 
                                data-kreyu--data-table-bundle--batch-target="selectAllCheckbox" 
                                type="checkbox"
                            />
                        </th>
                    HTML,
                    static::THEME_BOOTSTRAP => <<<HTML
                        <th class="text-center">
                            <input 
                                aria-label="Select all checkbox" 
                                class="form-check-input mt-0"
                                data-action="input-&gt;kreyu--data-table-bundle--batch#selectAll" 
                                data-identifier-name="id" 
                                data-kreyu--data-table-bundle--batch-target="selectAllCheckbox" 
                                type="checkbox"
                            />
                        </th>
                    HTML,
                    static::THEME_TABLER => <<<HTML
                        <th class="w-0">
                            <input 
                                aria-label="Select all checkbox" 
                                class="form-check-input mt-0"
                                data-action="input-&gt;kreyu--data-table-bundle--batch#selectAll" 
                                data-identifier-name="id" 
                                data-kreyu--data-table-bundle--batch-target="selectAllCheckbox" 
                                type="checkbox"
                            />
                        </th>
                    HTML,
                },
            ];
        }
    }

    public static function columnValueProvider(): iterable
    {
        foreach (static::THEMES as $themeName => $theme) {
            yield "with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'name' => 'id',
                    'data' => 1,
                ],
                'expectedHtml' => match ($theme) {
                    static::THEME_BASE => <<<HTML
                        <input 
                            type="checkbox" 
                            value="1" 
                            aria-label="Select all checkbox" 
                            data-index="0"
                            data-identifier-name="id" 
                            data-kreyu--data-table-bundle--batch-target="selectRowCheckbox" 
                            data-action="input-&gt;kreyu--data-table-bundle--batch#selectRow" 
                        />
                    HTML,
                    static::THEME_BOOTSTRAP, static::THEME_TABLER => <<<HTML
                        <div class="w-100 h-100 text-center">
                            <input 
                                type="checkbox" 
                                value="1" 
                                aria-label="Select all checkbox" 
                                data-index="0"
                                data-identifier-name="id" 
                                data-kreyu--data-table-bundle--batch-target="selectRowCheckbox" 
                                data-action="input-&gt;kreyu--data-table-bundle--batch#selectRow" 
                                class="form-check-input" 
                            />
                        </div>
                    HTML,
                },
            ];

            yield "with custom identifier name with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'name' => 'id',
                    'data' => 1,
                    'options' => [
                        'identifier_name' => 'uuid',
                    ],
                ],
                'expectedHtml' => match ($theme) {
                    static::THEME_BASE => <<<HTML
                        <input 
                            type="checkbox" 
                            value="1" 
                            aria-label="Select all checkbox" 
                            data-index="0"
                            data-identifier-name="uuid" 
                            data-kreyu--data-table-bundle--batch-target="selectRowCheckbox" 
                            data-action="input-&gt;kreyu--data-table-bundle--batch#selectRow" 
                        />
                    HTML,
                    static::THEME_BOOTSTRAP, static::THEME_TABLER => <<<HTML
                        <div class="w-100 h-100 text-center">
                            <input 
                                type="checkbox" 
                                value="1" 
                                aria-label="Select all checkbox" 
                                data-index="0"
                                data-identifier-name="uuid" 
                                data-kreyu--data-table-bundle--batch-target="selectRowCheckbox" 
                                data-action="input-&gt;kreyu--data-table-bundle--batch#selectRow" 
                                class="form-check-input" 
                            />
                        </div>
                    HTML,
                },
            ];
        }
    }
}
