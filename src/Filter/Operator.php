<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

enum Operator: string
{
    case Equal = 'equal';
    case NotEqual = 'not-equal';
    case Contain = 'contain';
    case NotContain = 'not-contain';
    case GreaterThan = 'greater-than';
    case GreaterThanEqual = 'greater-than-equal';
    case LessThan = 'less-than';
    case LessThanEqual = 'less-than-equal';
    case StartWith = 'start-with';
    case EndWith = 'end-with';

    public function getLabel(): string
    {
        return match ($this) {
            self::Equal => 'Equal',
            self::NotContain => 'Not contain',
            self::Contain => 'Contain',
            self::NotEqual => 'Not equal',
            self::GreaterThan => 'Greater than',
            self::GreaterThanEqual => 'Greater than or equal',
            self::LessThan => 'Less than',
            self::LessThanEqual => 'Less than or equal',
            self::StartWith => 'Start with',
            self::EndWith => 'End with',
        };
    }
}
