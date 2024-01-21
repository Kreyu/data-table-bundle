<script setup>
    import ColumnTypeOptions from "./options/column.md";
</script>

# DateTimeColumnType

The [`DateTimeColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/DateTimeColumnType.php) represents a column with value displayed as a date and time.

## Options

### `format`

- **type**: `string`
- **default**: `'d.m.Y H:i:s'`

The format specifier is the same as supported by [date](https://www.php.net/date).

### `timezone`

- **type**: `null` or `string`
- **default**: `null`

A timezone used to render the date time as string.

## Inherited options

<ColumnTypeOptions/>
