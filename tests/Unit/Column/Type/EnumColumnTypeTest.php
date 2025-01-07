<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\EnumColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\Type\ColumnTypeTestCase;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Enum\TranslatableEnum;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Enum\UnitEnum;
use Symfony\Contracts\Translation\TranslatorInterface;

class EnumColumnTypeTest extends ColumnTypeTestCase
{
    private ?TranslatorInterface $translator = null;

    protected function getTestedColumnType(): ColumnTypeInterface
    {
        return new EnumColumnType($this->translator);
    }

    protected function getAdditionalColumnTypes(): array
    {
        return [
            new TextColumnType(),
            new ColumnType(),
        ];
    }

    public function testDefaultFormatter(): void
    {
        $column = $this->createColumn();

        $columnValueView = $this->createColumnValueView($column, rowData: new class {
            public UnitEnum $enum = UnitEnum::Foo;
        });

        $this->assertEquals(UnitEnum::Foo, $columnValueView->vars['data']);
        $this->assertEquals('Foo', $columnValueView->vars['value']);
    }

    public function testDefaultFormatterWithTranslatableEnum(): void
    {
        $column = $this->createColumnWithTranslator();

        $columnValueView = $this->createColumnValueView($column, rowData: new class {
            public TranslatableEnum $enum = TranslatableEnum::Foo;
        });

        $this->assertEquals(TranslatableEnum::Foo, $columnValueView->vars['data']);
        $this->assertEquals('Translated foo', $columnValueView->vars['value']);
    }

    protected function createColumnWithTranslator(array $options = []): ColumnInterface
    {
        $this->translator = $this->createMock(TranslatorInterface::class);

        return parent::createColumn($options);
    }
}
