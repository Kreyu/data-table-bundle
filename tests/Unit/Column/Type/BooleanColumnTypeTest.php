<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\BooleanColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeUnitTestCase;

class BooleanColumnTypeTest extends ColumnTypeUnitTestCase
{
    protected function getTestedType(): string
    {
        return BooleanColumnType::class;
    }

    public static function optionsToValueViewVarsProvider(): iterable
    {
        yield [
            ['label_true' => 'foo'],
        ];

        yield [
            ['label_false' => 'bar'],
        ];
    }
}