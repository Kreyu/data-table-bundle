<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeTestCase;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeUnitTestCase;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Model\Product;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Model\Tag;

class CollectionColumnTypeTest extends ColumnTypeUnitTestCase
{
    private Product $data;

    protected function getTestedType(): string
    {
        return CollectionColumnType::class;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = new class () {};
        $this->data->tags = ['a', 'b']
    }

    public static function optionsToValueViewVarsProvider(): iterable
    {
        yield [
            ['separator' => ' - '],
        ];
    }

    public function testCollectionSizeDeterminesChildrenCountInValueViewVars(): void
    {
        $column = $this->createNamedColumn('tags');

        $view = $column->createValueView($this->createValueRowViewMock(data: $this->data));

        $this->assertCount(3, $view->vars['children']);
    }

    public function testNotProvidingPropertyPathNorGetterResultsInChildrenWithRawData(): void
    {
        $column = $this->createNamedColumn('tags');

        $view = $column->createValueView($this->createValueRowViewMock(data: $this->data));

        $this->assertEquals($this->data->tags[0], $view->vars['children'][0]->data);
        $this->assertEquals($this->data->tags[1], $view->vars['children'][1]->data);
        $this->assertEquals($this->data->tags[2], $view->vars['children'][2]->data);
    }

    public function testProvidingPropertyPathAsEntryOptionResultsInProperValueViewData(): void
    {
        $column = $this->createNamedColumn('tags', [
            'entry_options' => [
                'property_path' => 'name',
            ],
        ]);

        $view = $column->createValueView($this->createValueRowViewMock(data: $this->data));

        $this->assertEquals($this->data->tags[0]->name, $view->vars['children'][0]->data);
        $this->assertEquals($this->data->tags[1]->name, $view->vars['children'][1]->data);
        $this->assertEquals($this->data->tags[2]->name, $view->vars['children'][2]->data);
    }

    public function testProvidingGetterAsEntryOptionResultsInProperValueViewData(): void
    {
        $column = $this->createNamedColumn('tags', [
            'entry_options' => [
                'getter' => fn (Tag $tag) => $tag->name,
                'formatter' => fn (string $name) => strtoupper($name),
            ],
        ]);

        $view = $column->createValueView($this->createValueRowViewMock(data: $this->data));

        $this->assertEquals($this->data->tags[0]->name, $view->vars['children'][0]->data);
        $this->assertEquals($this->data->tags[1]->name, $view->vars['children'][1]->data);
        $this->assertEquals($this->data->tags[2]->name, $view->vars['children'][2]->data);
    }

    public function testProvidingFormatterAsEntryOptionResultsInProperValueViewValue(): void
    {
        $column = $this->createNamedColumn('tags', [
            'entry_options' => [
                'getter' => fn (Tag $tag) => $tag->name,
                'formatter' => fn (string $name) => strtoupper($name),
            ],
        ]);

        $view = $column->createValueView($this->createValueRowViewMock(data: $this->data));

        $this->assertEquals(strtoupper($this->data->tags[0]->name), $view->vars['children'][0]->value);
        $this->assertEquals(strtoupper($this->data->tags[1]->name), $view->vars['children'][1]->value);
        $this->assertEquals(strtoupper($this->data->tags[2]->name), $view->vars['children'][2]->value);
    }
}