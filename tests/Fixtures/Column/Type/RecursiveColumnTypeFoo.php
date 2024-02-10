<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\AbstractColumnType;

class RecursiveColumnTypeFoo extends AbstractColumnType
{
    public function getParent(): string
    {
        return RecursiveColumnTypeBar::class;
    }
}
