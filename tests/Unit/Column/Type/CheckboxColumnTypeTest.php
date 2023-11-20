<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\CheckboxColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeTestCase;
use Kreyu\Bundle\DataTableBundle\Test\Column\ColumnTypeUnitTestCase;

class CheckboxColumnTypeTest extends ColumnTypeUnitTestCase
{
    protected function getTestedType(): string
    {
        return CheckboxColumnType::class;
    }

    public static function optionsToValueViewVarsProvider(): iterable
    {
        yield [
            ['identifier_name' => 'foo'],
        ];
    }
}
