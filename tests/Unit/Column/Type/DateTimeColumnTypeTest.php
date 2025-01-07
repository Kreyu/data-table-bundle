<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\Type\ColumnTypeTestCase;

class DateTimeColumnTypeTest extends ColumnTypeTestCase
{
    protected function getTestedColumnType(): ColumnTypeInterface
    {
        return new DateTimeColumnType();
    }

    protected function getAdditionalColumnTypes(): array
    {
        return [
            new TextColumnType(),
            new ColumnType(),
        ];
    }

    public function testPassingFormatOption(): void
    {
        $column = $this->createColumn([
            'format' => 'd.m.Y H:i:s',
        ]);

        $valueView = $this->createColumnValueView($column);

        $this->assertEquals('d.m.Y H:i:s', $valueView->vars['format']);
    }

    public function testPassingFormatOptionWhenExportable(): void
    {
        $column = $this->createNamedColumn('dateTime', [
            'format' => 'd.m.Y H:i:s',
            'export' => true,
        ]);

        $data = new \stdClass();
        $data->dateTime = new \DateTimeImmutable('2021-01-01 00:00:00');

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: $data);

        $this->assertEquals($data->dateTime, $exportColumnValueView->vars['data']);
        $this->assertEquals('01.01.2021 00:00:00', $exportColumnValueView->vars['value']);
    }

    public function testPassingFormatOptionWhenExportableAndInvalidDate(): void
    {
        $column = $this->createNamedColumn('dateTime', [
            'format' => 'd.m.Y H:i:s',
            'export' => true,
        ]);

        $data = new \stdClass();
        $data->dateTime = 'invalid';

        $exportColumnValueView = $this->createExportColumnValueView($column, rowData: $data);

        $this->assertEquals('invalid', $exportColumnValueView->vars['data']);
        $this->assertEquals('', $exportColumnValueView->vars['value']);
    }

    public function testPassingTimezoneOption(): void
    {
        $column = $this->createColumn([
            'timezone' => 'Europe/Warsaw',
        ]);

        $valueView = $this->createColumnValueView($column);

        $this->assertEquals('Europe/Warsaw', $valueView->vars['timezone']);
    }
}
