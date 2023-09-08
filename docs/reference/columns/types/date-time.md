---
label: DateTime
order: e
tags:
  - columns
---

# DateTime column type

The `DateTimeColumnType` represents a column with value displayed as a date and time.

+-------------+---------------------------------------------------------------------+
| Parent type | [ColumnType](column)
+-------------+---------------------------------------------------------------------+
| Class       | [:icon-mark-github: DateTimeColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/DateTimeColumnType.php)
+-------------+---------------------------------------------------------------------+

## Options

### `format`

- **type**: `string`
- **default**: `'d.m.Y H:i:s'`

The format specifier is the same as supported by [date](https://www.php.net/date).

## Inherited options

{{ include '_column_options' }}
