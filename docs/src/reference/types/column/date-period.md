<script setup>
    import ColumnTypeOptions from "./options/column.md";
</script>

# DatePeriodColumnType

The [`DatePeriodColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/DatePeriodColumnType.php) represents a column with value displayed as a date (and with time by default).

## Options

### `format`

- **type**: `string`
- **default**: `'d.m.Y H:i:s'`

The format specifier is the same as supported by [date](https://www.php.net/date).

### `timezone`

- **type**: `null` or `string`
- **default**: `null`

A timezone used to render the dates as string.

### `separator`

- **type**: `string`
- **default**: `' - '`

Separator to display between the dates.

## Inherited options

<ColumnTypeOptions/>
