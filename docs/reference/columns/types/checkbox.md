---
label: Checkbox
order: k
tags:
  - columns
---

# Checkbox column type

The `CheckboxColumnType` represents a column with checkboxes, both in header and value rows.

This column is used for [batch actions](../../../features/actions/batch-actions.md) to let user select the desired rows. 

+-------------+---------------------------------------------------------------------+
| Parent type | [ColumnType](column)
+-------------+---------------------------------------------------------------------+
| Class       | [:icon-mark-github: CheckboxColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/CheckboxColumnType.php)
+-------------+---------------------------------------------------------------------+

## Options

### `identifier_name`

- **type**: `string`
- **default**: `'id'`

A name of the property to use in the batch actions.

For more details about this option's influence on the batch actions, see ["Changing the identifier parameter name"](../../../features/actions/batch-actions.md#changing-the-identifier-parameter-name) section.

## Inherited options

{{ option_label_default_value = '`\'□\'`' }}
{{ option_property_path_default_value = '`\'id\'`' }}

{{ include '_column_options' }}
