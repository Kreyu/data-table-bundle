# ColumnType

The [ColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ColumnType.php) represents a base column, used as a parent for every other type in the bundle.

## Options

### `label`

**type**: `string` or `Symfony\Component\Translation\TranslatableMessage` **default**: the label is "guessed" from the column name

Sets the label that will be used when rendering the column header.

### `label_translation_parameters`

**type**: `array` **default**: `[]`

Sets the parameters used when translating the `label` option.

### `translation_domain`

**type**: `false` or `string` **default**: the default `KreyuDataTable` is used

Sets the translation domain used when translating the column translatable values.  
Setting the option to `false` disables translation for the column.

### `property_path`

**type**: `null`, `false` or `string` **default**: `null` - the property path is "guessed" from the column name

Sets the property path used by the [PropertyAccessor](https://symfony.com/doc/current/components/property_access.html) to retrieve column value of each row.  
Setting the option to `false` disables property accessor (for situations, where you want to manually retrieve the value).

### `sort`

**type**: `bool` or `string` **default**: `false` - the sortable behavior is disabled

Sets the sort field used by the sortable behavior.   
Setting the option to `true` enables column sorting and uses the column name as a sort field name.  
Setting the option to `false` disables column sorting.

### `block_name`

**type**: `string` **default**: `kreyu_data_table_column_` + column type block prefix

Allows you to add a custom block name to the ones used by default to render the column type.
Useful for example if you have multiple instances of the same column type, and you need to personalize the rendering of the columns individually.

By default, if column type class name is `TextType`, the block name option will equal `kreyu_data_table_column_text`.

### `block_prefix`

**type**: `string` **default**: column type block prefix

Allows you to add a custom block prefix and override the block name used to render the column type.
Useful for example if you have multiple instances of the same column type, and you need to personalize the rendering of all of them without the need to create a new column type.

### `export`

**type**: `bool` **default**: `true` with some exceptions on built-in types (e.g. [ActionsType](actions.md))

If this value is true, the column will be included in the export results.

### `export_options`

**type**: `array` **default**: `[]`

Options used in exporting process. It can contain any option that is available for the column type.  
It is used to differentiate options for regular rendering, and excel rendering.

For example, if you wish to display quantity column with "Quantity" label, but export with a "Qty" header:

```php
$columns
    ->add('quantity', NumberType::class, [
        'label' => 'Quantity',
        'export_options' => [
            'label' => 'Qty',
        ],    
    ])
;
```
