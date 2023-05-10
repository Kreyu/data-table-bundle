---
label: Form
visibility: hidden
order: h
---

# Form column type

The `FormColumnType` represents a column with value displayed as a form input.

+-------------+---------------------------------------------------------------------+
| Parent type | [ColumnType](column)
+-------------+---------------------------------------------------------------------+
| Class       | [:icon-mark-github: FormColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/FormColumnType.php)
+-------------+---------------------------------------------------------------------+

## Options

### `form`

- **type**: `string`
- **default**: `'Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType'`

An instance of form that contains a collection of fields to render in the column.

### `entry_options`

- **type**: `false`, `null` or `string`
- **default**: `null` - the child path is "guessed" from the column name

This is the path to the child form of each collection field. For example, if you have a collection of `ProductType` 
which contains `name` and `quantity` fields, and you want to display the quantity field on the column, this option value should equal `quantity`.

Setting this option to `false` disables this functionality and renders the form directly.

## Inherited options

{{ include '_column_options' }}
