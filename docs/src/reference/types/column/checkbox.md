<script setup>
    import ColumnTypeOptions from "./options/column.md";
</script>

# CheckboxColumnType

The [`CheckboxColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/CheckboxColumnType.php) represents a column with checkboxes, both in header and value rows.

::: info In most cases, it is not necessary to use this column type directly.
Instead, use data table builder's `addBatchAction()` method.
If at least one batch action is defined and is visible, an `BatchActionType` is added to the data table.

For details, see [adding checkbox column](../../../docs/components/actions.md#adding-checkbox-column) section of the action documentation.
:::

## Options

### `identifier_name`

- **type**: `string`
- **default**: `'id'`

A name of the property to use in the batch actions.

For more details about this option's influence on the batch actions, see ["Changing the identifier parameter name"](#) section.

## Inherited options

<ColumnTypeOptions :defaults="{
    label: `'□'`,
    property_path: `'id'`,
}" />
