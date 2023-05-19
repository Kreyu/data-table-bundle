---
label: DatePeriod
order: f
---

# DatePeriod column type

The `DatePeriodColumnType` represents a column with value displayed as a date (and with time by default).

+-------------+---------------------------------------------------------------------+
| Parent type | [ColumnType](column)
+-------------+---------------------------------------------------------------------+
| Class       | [:icon-mark-github: DatePeriodColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/DatePeriodColumnType.php)
+-------------+---------------------------------------------------------------------+

## Options

### `format`

- **type**: `string`
- **default**: `'d.m.Y H:i:s'`

The format specifier is the same as supported by [date](https://www.php.net/date).

### `format`

- **type**: `null` or `string`
- **default**: `null`

Sets the timezone passed to the date formatter.

## Inherited options

{{ include '_column_options' }}
