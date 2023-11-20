<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Rendering\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeRenderingTestCase;

class TextColumnTypeRenderingTest extends ColumnTypeRenderingTestCase
{
    protected function getTestedType(): string
    {
        return TextColumnType::class;
    }

    public static function columnHeaderProvider(): iterable
    {
        yield from ColumnTypeRenderingTest::columnHeaderProvider();
    }

    public static function columnValueProvider(): iterable
    {
        yield from ColumnTypeRenderingTest::columnValueProvider();
    }
}
