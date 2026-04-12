<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\RowGroup;

enum AggregationType: string
{
    case Count = 'count';
    case Sum = 'sum';
    case Average = 'average';
    case Min = 'min';
    case Max = 'max';
}
