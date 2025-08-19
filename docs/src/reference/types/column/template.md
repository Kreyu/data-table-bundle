<script setup>
    import ColumnTypeOptions from "./options/column.md";
</script>

# TemplateColumnType

The [`TemplateColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/TemplateColumnType.php) represents a column with value displayed as a Twig template.

## Options

### `template_path`

- **type**: `string` or `callable`

Sets the path to the template that should be rendered.

### `template_vars`

- **type**: `string` or `callable`

Sets the variables used within the template.

## Inherited options

<ColumnTypeOptions/>
