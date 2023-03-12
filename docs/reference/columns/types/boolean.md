# BooleanColumnType

The [:material-github: BooleanColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/BooleanColumnType.php) column represents a column with value displayed as a "yes" or "no" badge.

## Options

### `label_true`

**type**: `string` or `Symfony\Component\Translation\TranslatableMessage` **default**: `'Yes'`

Sets the value that will be displayed if row value is true.

### `label_false`

**type**: `string` or `Symfony\Component\Translation\TranslatableMessage` **default**: `'No'`

Sets the value that will be displayed if row value is false.

## Inherited options

{% include-markdown "_column_options.md" heading-offset=2 %}
