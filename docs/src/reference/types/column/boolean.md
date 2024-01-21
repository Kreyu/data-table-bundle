<script setup>
    import ColumnTypeOptions from "./options/column.md";
</script>

# BooleanColumnType

The [`BooleanColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/NumberColumnType.php) represents a column with value displayed as a "yes" or "no".

## Options

### `label_true`

- **type**: `string` or `Symfony\Component\Translation\TranslatableInterface`
- **default**: `'Yes'`

Sets the value that will be displayed if value is truthy.

### `label_false`

- **type**: `string` or `Symfony\Component\Translation\TranslatableInterface`
- **default**: `'No'`

Sets the value that will be displayed if row value is falsy.

## Inherited options

<ColumnTypeOptions/>
