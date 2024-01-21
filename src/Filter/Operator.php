<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

enum Operator: string
{
    case Equals = 'equals';
    case NotEquals = 'not-equals';
    case Contains = 'contains';
    case NotContains = 'not-contains';
    case In = 'in';
    case NotIn = 'not-in';
    case GreaterThan = 'greater-than';
    case GreaterThanEquals = 'greater-than-equals';
    case LessThan = 'less-than';
    case LessThanEquals = 'less-than-equals';
    case StartsWith = 'starts-with';
    case EndsWith = 'ends-with';
    case Between = 'between';

    public function getLabel(): string
    {
        return match ($this) {
            self::Equals => 'Equals',
            self::NotContains => 'Not contains',
            self::Contains => 'Contains',
            self::NotEquals => 'Not equals',
            self::In => 'In',
            self::NotIn => 'Not in',
            self::GreaterThan => 'Greater than',
            self::GreaterThanEquals => 'Greater than or equals',
            self::LessThan => 'Less than',
            self::LessThanEquals => 'Less than or equals',
            self::StartsWith => 'Starts with',
            self::EndsWith => 'Ends with',
            self::Between => 'Between',
        };
    }
}
