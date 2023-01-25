<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

enum Operator: string
{
    case EQUALS = 'equals';
    case CONTAINS = 'contains';
    case NOT_CONTAINS = 'not-contains';
    case NOT_EQUALS = 'not-equals';
    case GREATER_THAN = 'greater-than';
    case GREATER_THAN_EQUALS = 'greater-than-equals';
    case LESS_THAN_EQUALS = 'less-than-equals';
    case LESS_THAN = 'less-than';
    case STARTS_WITH = 'starts-with';
    case ENDS_WITH = 'ends-with';
}
