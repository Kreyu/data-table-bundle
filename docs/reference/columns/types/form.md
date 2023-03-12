# FormColumnType

The [:material-github: FormColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/FormColumnType.php) column represents a column with value displayed as a form input.

For more details about how to use this column type, see [integration with Symfony Forms](../../../advanced/integration-with-symfony-forms.md). 

## Options

### `form`

**type**: `null` or `Symfony\Component\Form\FormInterface`

This is the form that contains a collection of fields to display in the column. 

### `form_child_path`

**type**: `null`, `false` or `string` **default**: `null` - the child path is "guessed" from the column name

This is the path to the child form of each collection field.
For example, if you have a collection of `ProductType` which contains `name` and `quantity` fields, 
and you want to display the `quantity` field on the column, this option value should equal `quantity`.

Setting this option to `false` disables this functionality and renders the collection field directly.

## Inherited options

{% include-markdown "_column_options.md" heading-offset=2 %}