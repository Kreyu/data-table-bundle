---
label: Template
visibility: hidden
order: i
---

# Template column type

The `TemplateColumnType` represents a column with value displayed as a Twig template.

+-------------+---------------------------------------------------------------------+
| Parent type | [ColumnType](column)
+-------------+---------------------------------------------------------------------+
| Class       | [:icon-mark-github: TemplateColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/TemplateColumnType.php)
+-------------+---------------------------------------------------------------------+

## Options

### `template_path`

- **type**: `string` or `callable`

Sets the path to the template that should be rendered.

### `template_vars`

- **type**: `string` or `callable`

Sets the variables used within the template.

## Inherited options

{{ include '_column_options' }}
