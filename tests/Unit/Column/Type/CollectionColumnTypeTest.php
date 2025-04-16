<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\EnumColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\Type\ColumnTypeTestCase;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Enum\TranslatableEnum;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Enum\UnitEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\Translation\t;

class CollectionColumnTypeTest extends ColumnTypeTestCase
{
    private ?TranslatorInterface $translator = null;

    protected function getTestedColumnType(): ColumnTypeInterface
    {
        return new CollectionColumnType($this->translator);
    }

    protected function getAdditionalColumnTypes(): array
    {
        return [
            new DateColumnType(),
            new DateTimeColumnType(),
            new EnumColumnType(),
            new NumberColumnType(),
            new TextColumnType(),
            new ColumnType(),
        ];
    }

    public function testPassingSeparatorOptionAsString(): void
    {
        $column = $this->createColumn([
            'separator' => '|',
            'export' => true,
        ]);

        $valueView = $this->createColumnValueView($column);

        $this->assertEquals('|', $valueView->vars['separator']);

        $rowData = new class {
            public array $collection = [1, 2, 3];
        };

        $exportValueView = $this->createExportColumnValueView($column, rowData: $rowData);

        $this->assertEquals('1|2|3', $exportValueView->vars['value']);
    }

    public function testPassingSeparatorOptionAsTranslatable(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->translator->expects($this->once())
            ->method('trans')
            ->with('separator', [], 'data-table')
            ->willReturn('translated');

        $column = $this->createColumn([
            'separator' => $separator = t('separator', domain: 'data-table'),
            'export' => true,
        ]);

        $valueView = $this->createColumnValueView($column);

        $this->assertEquals($separator, $valueView->vars['separator']);
        $this->assertTrue($valueView->vars['separator_translatable']);

        $rowData = new class {
            public array $collection = [1, 2, 3];
        };

        $exportValueView = $this->createExportColumnValueView($column, rowData: $rowData);

        $this->assertEquals('1translated2translated3', $exportValueView->vars['value']);
    }

    public function testPassingSeparatorOptionAsNull(): void
    {
        $column = $this->createColumn([
            'separator' => null,
            'export' => true,
        ]);

        $valueView = $this->createColumnValueView($column);

        $this->assertNull($valueView->vars['separator']);

        $rowData = new class {
            public array $collection = [1, 2, 3];
        };

        $exportValueView = $this->createExportColumnValueView($column, rowData: $rowData);

        $this->assertEquals('123', $exportValueView->vars['value']);
    }

    public function testDefaultSeparatorHtmlOption(): void
    {
        $column = $this->createColumn();

        $valueView = $this->createColumnValueView($column);

        $this->assertFalse($valueView->vars['separator_html']);
    }

    public function testPassingSeparatorHtmlOption(): void
    {
        $column = $this->createColumn([
            'separator_html' => true,
        ]);

        $valueView = $this->createColumnValueView($column);

        $this->assertTrue($valueView->vars['separator_html']);
    }

    public function testCreatesChildren(): void
    {
        $column = $this->createColumn([
            'entry_type' => NumberColumnType::class,
            'entry_options' => [
                'use_intl_formatter' => false,
            ],
        ]);

        $data = new class {
            public array $collection = [1, 2, 3];
        };

        $valueRowView = $this->createValueRowView(data: $data);
        $columnValueView = $this->createColumnValueView($column, $valueRowView);

        $this->assertCount(3, $columnValueView->vars['children']);
        $this->assertContainsOnlyInstancesOf(ColumnValueView::class, $columnValueView->vars['children']);

        for ($i = 0; $i <= 2; ++$i) {
            /** @var ColumnValueView $child */
            $child = $columnValueView->vars['children'][$i];

            $expectedValueRowView = clone $valueRowView;
            $expectedValueRowView->origin = $valueRowView;
            $expectedValueRowView->index = $i;
            $expectedValueRowView->data = $data->collection[$i];

            $this->assertEquals((string) $i, $child->vars['name']);
            $this->assertEquals($expectedValueRowView, $child->vars['row']);
            $this->assertFalse($child->vars['use_intl_formatter']);
            $this->assertEquals($child->getDataTable(), $columnValueView->getDataTable());
        }
    }

    #[DataProvider('provideNonScalarValuesCases')]
    public function testWithNonScalarValues(array $collection): void
    {
        $column = $this->createColumn();

        $data = new class($collection) {
            public function __construct(
                public array $collection,
            ) {
            }
        };

        $valueRowView = $this->createValueRowView(data: $data);
        $columnValueView = $this->createColumnValueView($column, $valueRowView);

        $this->assertEquals($collection[0], $columnValueView->vars['children'][0]->vars['value']);
        $this->assertEquals($collection[1], $columnValueView->vars['children'][1]->vars['value']);
    }

    public static function provideNonScalarValuesCases(): iterable
    {
        yield 'DateTime' => [[new \DateTime('2020-01-01'), new \DateTimeImmutable('2021-02-02')]];

        yield 'Enums' => [[UnitEnum::Foo, TranslatableEnum::Bar]];

        yield 'stdClass' => [[new \stdClass(), (object) ['foo' => 'bar']]];
    }
}
