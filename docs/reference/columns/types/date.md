---
label: Date
order: e
tags:
  - columns
---

# Date column type

The `DateColumnType` represents a column with value displayed as a date.

This column type works exactly like `DateTimeColumnType`, but has a different default format.

+-------------+---------------------------------------------------------------------+
| Parent type | [DateTimeType](date-time.md)
+-------------+---------------------------------------------------------------------+
| Class       | [:icon-mark-github: DateColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/DateColumnType.php)
+-------------+---------------------------------------------------------------------+

## Options

### `format`

- **type**: `string`
- **default**: `'d.m.Y'`

The format specifier is the same as supported by [date](https://www.php.net/date).

## Inherited options

{{ include '_column_options' }}
