---
label: Boolean
order: c
---

# Boolean column type

The `BooleanColumnType` represents a column with value displayed as a "yes" or "no".

+-------------+---------------------------------------------------------------------+
| Parent type | [ColumnType](column)
+-------------+---------------------------------------------------------------------+
| Class       | [:icon-mark-github: BooleanColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/BooleanColumnType.php)
+-------------+---------------------------------------------------------------------+

## Options

### `label_true`

- **type**: `string` or `Symfony\Component\Translation\TranslatableMessage`
- **default**: `'Yes'`

Sets the value that will be displayed if value is truthy.

### `label_false`

- **type**: `string` or `Symfony\Component\Translation\TranslatableMessage`
- **default**: `'No'`

Sets the value that will be displayed if row value is falsy.

## Inherited options

{{ include '_column_options' }}
