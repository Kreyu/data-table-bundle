# EntityFilter (Doctrine ORM)

The [EntityFilter](../../../../src/Bridge/Doctrine/Orm/Filter/EntityFilter.php) represents a filter that operates on identifier values.

Displayed as a selector, allows the user to select specific entity loaded from the database, to query by its identifier.

## Supported operators

- `Operator::EQUAL`
- `Operator::NOT_EQUAL`
- `Operator::CONTAINS`
- `Operator::NOT_CONTAINS`

## Options

This filter has no additional options.

## Inherited options

See [base filter type documentation](../../filter.md).

## Overridden options

### `field_type`

**type**: `string` **default**: `'Symfony\Bridge\Doctrine\Form\Type\EntityType'`
