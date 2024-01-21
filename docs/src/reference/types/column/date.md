<script setup>
    import ColumnTypeOptions from "./options/column.md";
</script>

# DateColumnType

The [`DateColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/DateColumnType.php) represents a column with value displayed as a date.

This column type works exactly like `DateTimeColumnType`, but has a different default format.

## Options

### `format`

- **type**: `string`
- **default**: `'d.m.Y'`

The format specifier is the same as supported by [date](https://www.php.net/date).

### `timezone`

- **type**: `null` or `string`
- **default**: `null`

A timezone used to render the date time as string.

## Inherited options

<ColumnTypeOptions/>
