<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Rendering\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\TemplateColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeRenderingTestCase;

class TemplateColumnTypeRenderingTest extends ColumnTypeRenderingTestCase
{
    protected function getTestedType(): string
    {
        return TemplateColumnType::class;
    }

    public static function columnHeaderProvider(): iterable
    {
        $entries = ColumnTypeRenderingTest::columnHeaderProvider();

        foreach ($entries as $description => $entry) {
            $entry['column']['options']['template_path'] = '@KreyuDataTableTest/column/template.html.twig';

            yield $description => $entry;
        }
    }

    public static function columnValueProvider(): iterable
    {
        foreach (static::THEMES as $themeName => $theme) {
            yield "template with variables with $themeName theme" => [
                'theme' => $theme,
                'column' => [
                    'options' => [
                        'template_path' => '@KreyuDataTableTest/column/template.html.twig',
                        'template_vars' => ['foo' => 'bar'],
                    ],
                ],
                'expectedHtml' => '<span>Custom template: bar</span>',
            ];
        }
    }
}
