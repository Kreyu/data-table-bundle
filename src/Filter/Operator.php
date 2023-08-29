<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

enum Operator: string
{
    case Equals = 'equals';
    case NotEquals = 'not-equals';
    case Contains = 'contains';
    case NotContains = 'not-contains';
    case GreaterThan = 'greater-than';
    case GreaterThanEquals = 'greater-than-equals';
    case LessThan = 'less-than';
    case LessThanEquals = 'less-than-equals';
    case StartsWith = 'starts-with';
    case EndsWith = 'ends-with';

    // TODO: Remove deprecated cases

    /**
     * @deprecated use {@see Operator::Equals} instead
     */
    case EQUALS = 'deprecated-equals';

    /**
     * @deprecated use {@see Operator::NotEquals} instead
     */
    case CONTAINS = 'deprecated-contains';

    /**
     * @deprecated use {@see Operator::Contains} instead
     */
    case NOT_CONTAINS = 'deprecated-not-contains';

    /**
     * @deprecated use {@see Operator::NotContains} instead
     */
    case NOT_EQUALS = 'deprecated-not-equals';

    /**
     * @deprecated use {@see Operator::GreaterThan} instead
     */
    case GREATER_THAN = 'deprecated-greater-than';

    /**
     * @deprecated use {@see Operator::GreaterThanEquals} instead
     */
    case GREATER_THAN_EQUALS = 'deprecated-greater-than-equals';

    /**
     * @deprecated use {@see Operator::LessThan} instead
     */
    case LESS_THAN = 'deprecated-less-than';

    /**
     * @deprecated use {@see Operator::LessThanEquals} instead
     */
    case LESS_THAN_EQUALS = 'deprecated-less-than-equals';

    /**
     * @deprecated use {@see Operator::StartsWith} instead
     */
    case STARTS_WITH = 'deprecated-starts-with';

    /**
     * @deprecated use {@see Operator::EndsWith} instead
     */
    case ENDS_WITH = 'deprecated-ends-with';

    public function getLabel(): string
    {
        // TODO: Remove deprecated cases labels
        return match ($this) {
            self::EQUALS, self::Equals => 'Equals',
            self::NOT_CONTAINS, self::NotContains => 'Not contains',
            self::CONTAINS, self::Contains => 'Contains',
            self::NOT_EQUALS, self::NotEquals => 'Not equals',
            self::GREATER_THAN, self::GreaterThan => 'Greater than',
            self::GREATER_THAN_EQUALS, self::GreaterThanEquals => 'Greater than or equal',
            self::LESS_THAN, self::LessThan => 'Less than',
            self::LESS_THAN_EQUALS, self::LessThanEquals => 'Less than or equal',
            self::STARTS_WITH, self::StartsWith => 'Starts with',
            self::ENDS_WITH, self::EndsWith => 'Ends with',
        };
    }

    /**
     * TODO: Remove this method after removing deprecated cases.
     */
    public function getNonDeprecatedCase(): self
    {
        return match ($this) {
            self::EQUALS => self::Equals,
            self::NOT_CONTAINS => self::NotContains,
            self::CONTAINS => self::Contains,
            self::NOT_EQUALS => self::NotEquals,
            self::GREATER_THAN => self::GreaterThan,
            self::GREATER_THAN_EQUALS => self::GreaterThanEquals,
            self::LESS_THAN => self::LessThan,
            self::LESS_THAN_EQUALS => self::LessThanEquals,
            self::STARTS_WITH => self::StartsWith,
            self::ENDS_WITH => self::EndsWith,
            default => $this,
        };
    }
}
