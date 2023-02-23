# DateTimeType

The [DateTimeType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/DateTimeType.php) column represents a column with a value displayed as a readable date time string.

## Options

### `format`

**type**: `string` **default**: `'d.m.Y H:i:s'`

The format specifier is the same as supported by [date](https://www.php.net/date), except when the filtered data is of type [DateInterval](https://www.php.net/DateInterval), 
when the format must conform to [DateInterval::format](https://www.php.net/DateInterval.format) instead.

### `timezone`

**type**: `null` or `string` **default**: `null`

Sets the timezone passed to the date formatters.

## Inherited options

See [base column type documentation](column.md).
