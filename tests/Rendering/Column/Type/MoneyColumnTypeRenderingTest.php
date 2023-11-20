<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Rendering\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\MoneyColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeRenderingTestCase;

class MoneyColumnTypeRenderingTest extends ColumnTypeRenderingTestCase
{
    protected function getTestedType(): string
    {
        return MoneyColumnType::class;
    }

    public static function columnHeaderProvider(): iterable
    {
        $entries = ColumnTypeRenderingTest::columnHeaderProvider();

        foreach ($entries as $description => $entry) {
            $entry['column']['options']['currency'] = 'PLN';

            yield $description => $entry;
        }
    }

    public static function columnValueProvider(): iterable
    {
        foreach (static::THEMES as $theme) {
            yield "with $theme theme" => [
                'theme' => $theme,
                'column' => [
                    'data' => 1000,
                    'options' => [
                        'currency' => 'PLN',
                    ],
                ],
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
