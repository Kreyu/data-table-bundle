<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

enum Operator: string
{
    case EQUAL = 'equal';
    case CONTAINS = 'contains';
    case NOT_CONTAINS = 'not-contains';
    case NOT_EQUAL = 'not-equal';
    case GREATER_EQUAL = 'greater-equal';
    case LESS_EQUAL = 'less-equal';
    case GREATER_THAN = 'greater-than';
    case LESS_THAN = 'less-than';
}
