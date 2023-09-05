<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Type;

use Kreyu\Bundle\DataTableBundle\Test\DataTableTypeTestCase;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class DataTableTypeTest extends DataTableTypeTestCase
{
    protected function getTestedType(): string
    {
        return DataTableType::class;
    }
}
