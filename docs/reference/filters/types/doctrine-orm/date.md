{% set option_field_type_default = 'Symfony\Component\Form\Extension\Core\Type\DateType' %}

# DateFilterType

The [DateFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/Type/DateFilterType.php) represents a filter that operates on date values.

## Supported operators

- `Operator::EQUALS`
- `Operator::NOT_EQUALS`
- `Operator::GREATER_THAN`
- `Operator::GREATER_THAN_EQUALS`
- `Operator::LESS_THAN`
- `Operator::LESS_THAN_EQUALS`

## Options

This filter has no additional options.

## Inherited options

{% include-markdown "../_filter_options.md" heading-offset=2 %}