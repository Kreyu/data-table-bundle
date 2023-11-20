<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Rendering\Column\Type;

use Kreyu\Bundle\DataTableBundle\Action\Type\ActionType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\DataTables;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeRenderingTestCase;

class ActionsColumnTypeRenderingTest extends ColumnTypeRenderingTestCase
{
    protected function getTestedType(): string
    {
        return ActionsColumnType::class;
    }

    public static function columnHeaderProvider(): iterable
    {
        $entries = ColumnTypeRenderingTest::columnHeaderProvider();

        foreach ($entries as $description => $entry) {
            $entry['column']['options']['actions'] = [[
                'type' => ActionType::class,
                'type_options' => [],
                'visible' => true
            ]];

            yield $description => $entry;
        }
    }

    public static function columnValueProvider(): iterable
    {
        $actionFactory = DataTables::createActionFactory();

        foreach (static::THEMES as $theme) {
            yield "Actions with $theme theme" => [
                'theme' => $theme,
                'column' => [
                    'options' => [
                        'actions' => [
                            'test' => $actionFactory->create(options: [
                                'translation_domain' => 'test',
                            ]),
                        ],
                    ]
                ],
                'expectedHtml' => match ($theme) {
                    static::THEME_BASE => 'Action',
                    static::THEME_BOOTSTRAP, static::THEME_TABLER => '<div class="d-inline-block">Action</div>',
                },
            ];

            yield "Actions with icon with $theme theme" => [
                'theme' => $theme,
                'column' => [
                    'options' => [
                        'actions' => [
                            'test' => $actionFactory->create(options: [
                                'translation_domain' => 'test',
                                'icon_attr' => [
                                    'class' => 'fa fa-icon',
                                ],
                            ]),
                        ],
                    ]
                ],
                'expectedHtml' => match ($theme) {
                    static::THEME_BASE => 'Action',
                    static::THEME_BOOTSTRAP, static::THEME_TABLER => <<<HTML
                        <div class="d-inline-block"><i class="fa fa-icon"/>Action</div>
                    HTML,
                },
            ];

            yield "Hidden actions with $theme theme" => [
                'theme' => $theme,
                'column' => [
                    'options' => [
                        'actions' => [
                            'test' => $actionFactory->create(options: [
                                'translation_domain' => 'test',
                                'visible' => false,
                            ]),
                        ],
                    ]
                ],
                'expectedHtml' => '',
            ];
        }
    }
}
