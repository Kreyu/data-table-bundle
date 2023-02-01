# NumericFilter (Doctrine ORM)

The [NumericFilter](../../../../src/Bridge/Doctrine/Orm/Filter/NumericFilter.php) represents a filter that operates on numeric values.

## Supported operators

- `Operator::EQUAL`
- `Operator::NOT_EQUAL`
- `Operator::GREATER_EQUAL`
- `Operator::GREATER_THAN`
- `Operator::LESS_EQUAL`
- `Operator::LESS_THAN`

## Options

This filter has no additional options.

## Inherited options

See [base filter type documentation](../../filter.md).

## Overridden options

### `field_type`

**type**: `string` **default**: `'Symfony\Component\Form\Extension\Core\Type\NumberType'`
