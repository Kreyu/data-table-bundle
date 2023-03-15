{% set option_field_type_default = 'Symfony\Bridge\Doctrine\Form\Type\EntityType' %}

# EntityFilterType

The [EntityFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/Type/EntityFilterType.php) represents a filter that operates on identifier values.

Displayed as a selector, allows the user to select specific entity loaded from the database, to query by its identifier.

## Supported operators

- `Operator::EQUALS`
- `Operator::NOT_EQUALS`
- `Operator::CONTAINS`
- `Operator::NOT_CONTAINS`

## Options

This filter has no additional options.

## Inherited options

{% include-markdown "../_filter_options.md" heading-offset=2 %}