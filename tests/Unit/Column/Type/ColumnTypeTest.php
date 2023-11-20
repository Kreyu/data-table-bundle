<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeTestCase;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeUnitTestCase;

class ColumnTypeTest extends ColumnTypeUnitTestCase
{
    protected function getTestedType(): string
    {
        return ColumnType::class;
    }

    public static function optionsToHeaderViewVarsProvider(): iterable
    {
        yield [
            ['block_prefix' => 'foo'],
            ['block_prefixes' => ['foo', 'column']],
        ];

        yield [
            ['label' => 'foo'],
        ];

        yield [
            ['header_translation_domain' => 'foo'],
            ['translation_domain' => 'foo'],
        ];

        yield [
            ['header_translation_parameters' => ['foo', 'bar']],
            ['translation_parameters' => ['foo', 'bar']],
        ];

        yield [
            ['header_attr' => ['foo' => 'bar']],
            ['attr' => ['foo' => 'bar']],
        ];
    }

    public static function optionsToValueViewVarsProvider(): iterable
    {
        yield [
            ['block_prefix' => 'foo'],
            ['block_prefixes' => ['foo', 'column']],
        ];

        yield [
            ['value_translation_domain' => 'foo'],
            ['translation_domain' => 'foo'],
        ];

        yield [
            ['value_translation_parameters' => ['foo', 'bar']],
            ['translation_parameters' => ['foo', 'bar']],
        ];

        yield [
            ['value_attr' => ['foo' => 'bar']],
            ['attr' => ['foo' => 'bar']],
        ];

        yield [
            ['value_attr' => fn ($normData, $rowData) => ['foo' => 'bar']],
            ['attr' => ['foo' => 'bar']],
        ];
    }

    public function testPassingLabelAsOption(): void
    {
        $column = $this->createColumn(['label' => 'foo']);

        $view = $column->createHeaderView($this->createHeaderRowViewMock());

        $this->assertEquals('foo', $view->vars['label']);
    }

    public function testPassingHeaderTranslationDomainAsOption(): void
    {
        $column = $this->createColumn(['header_translation_domain' => 'foo']);

        $view = $column->createHeaderView($this->createHeaderRowViewMock());

        $this->assertEquals('foo', $view->vars['translation_domain']);
    }

    public function testPassingHeaderTranslationParametersAsOption(): void
    {
        $column = $this->createColumn(['header_translation_parameters' => ['foo', 'bar']]);

        $view = $column->createHeaderView($this->createHeaderRowViewMock());

        $this->assertEquals(['foo', 'bar'], $view->vars['translation_parameters']);
    }

    public function testPassingValueTranslationDomainAsOption(): void
    {
        $column = $this->createColumn(['value_translation_domain' => 'foo']);

        $view = $column->createValueView($this->createValueRowViewMock());

        $this->assertEquals('foo', $view->vars['translation_domain']);
    }

    public function testPassingValueTranslationParametersAsOption(): void
    {
        $column = $this->createColumn(['value_translation_parameters' => ['foo', 'bar']]);

        $view = $column->createValueView($this->createValueRowViewMock());

        $this->assertEquals(['foo', 'bar'], $view->vars['translation_parameters']);
    }

    public function testPassingBlockPrefixAsOption(): void
    {
        $column = $this->createColumn(['block_prefix' => 'foo']);

        $views = [
            $column->createHeaderView($this->createHeaderRowViewMock()),
            $column->createValueView($this->createValueRowViewMock()),
        ];

        foreach ($views as $view) {
            $this->assertEquals(['foo', 'column'], $view->vars['block_prefixes']);
        }
    }

    public function testPassingSortAsTrueAsOption(): void
    {
        $column = $this->createColumn(['sort' => true]);

        $this->assertTrue($column->getConfig()->isSortable());
    }

    public function testPassingSortAsStringAsOption(): void
    {
        $column = $this->createColumn(['sort' => 'foo']);

        $this->assertTrue($column->getConfig()->isSortable());
        $this->assertEquals('foo', (string) $column->getConfig()->getSortPropertyPath());
    }

    public function testPassingExportAsTrueAsOption(): void
    {
        $column = $this->createColumn(['export' => true]);

        $this->assertTrue($column->getConfig()->isExportable());
    }

    public function testPassingExportAsArrayAsOption(): void
    {
        $column = $this->createColumn(['export' => []]);

        $this->assertTrue($column->getConfig()->isExportable());
    }

    public function testPassingFormatterAsOption(): void
    {
        $column = $this->createColumn(['formatter' => fn ($value) => $value.'_bar']);

        $view = $column->createValueView($this->createValueRowViewMock('foo'));

        $this->assertEquals('foo_bar', $view->value);
    }

    public function testPassingPropertyPathAsOption(): void
    {
        $column = $this->createColumn(['property_path' => '[foo]']);

        $view = $column->createValueView($this->createValueRowViewMock(['foo' => 'bar']));

        $this->assertEquals('bar', $view->value);
    }

    public function testPassingGetterAsOption(): void
    {
        $column = $this->createColumn(['getter' => fn ($value) => $value['foo']]);

        $view = $column->createValueView($this->createValueRowViewMock(['foo' => 'bar']));

        $this->assertEquals('bar', $view->value);
    }

    public function testPassingHeaderAttrAsOption(): void
    {
        $column = $this->createColumn(['header_attr' => ['foo' => 'bar']]);

        $view = $column->createHeaderView($this->createHeaderRowViewMock());

        $this->assertEquals(['foo' => 'bar'], $view->vars['attr']);
    }

    public function testPassingValueAttrAsOption(): void
    {
        $column = $this->createColumn(['value_attr' => ['foo' => 'bar']]);

        $view = $column->createValueView($this->createValueRowViewMock());

        $this->assertEquals(['foo' => 'bar'], $view->vars['attr']);
    }

    public function testPassingPriorityAsOption(): void
    {
        $column = $this->createColumn(['priority' => 10]);

        $this->assertEquals(10, $column->getPriority());
    }

    public function testPassingVisibleAsOption(): void
    {
        $column = $this->createColumn(['visible' => false]);

        $this->assertFalse($column->isVisible());
    }

    public function testPassingPersonalizableAsOption(): void
    {
        $column = $this->createColumn(['personalizable' => false]);

        $this->assertFalse($column->getConfig()->isPersonalizable());
    }

    public function testPropertyPathInheritsFromColumnName(): void
    {
        $column = $this->createNamedColumn('foo');

        $this->assertEquals('foo', $column->getPropertyPath());
    }

    public function testSortPropertyPathInheritsFromColumnName(): void
    {
        $column = $this->createNamedColumn('foo');

        $this->assertEquals('foo', $column->getSortPropertyPath());
    }

    public function testSortPropertyPathInheritsFromPropertyPath(): void
    {
        $column = $this->createColumn(['property_path' => 'foo']);

        $this->assertEquals('foo', $column->getSortPropertyPath());
    }

    public function testLabelInheritsFromColumnNameInSentenceCase(): void
    {
        $column = $this->createNamedColumn('fooBar');

        $view = $column->createHeaderView($this->createHeaderRowViewMock());

        $this->assertEquals('Foo bar', $view->vars['label']);
    }

    public function testColumnHeaderViewTranslationDomainInheritsFromDataTableTranslationDomain(): void
    {
        $column = $this->createColumn();

        $headerRowView = $this->createHeaderRowViewMock();
        $headerRowView->parent->vars['translation_domain'] = 'foo';

        $view = $column->createHeaderView($headerRowView);

        $this->assertEquals('foo', $view->vars['translation_domain']);
    }

    public function testPassingValueTranslationDomainOptionAsNullInheritsFromDataTableTranslationDomain(): void
    {
        $column = $this->createColumn(['value_translation_domain' => null]);

        $valueRowView = $this->createValueRowViewMock();
        $valueRowView->parent->vars['translation_domain'] = 'foo';

        $view = $column->createValueView($valueRowView);

        $this->assertEquals('foo', $view->vars['translation_domain']);
    }
}
