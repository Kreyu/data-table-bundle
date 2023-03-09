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

### `getter`

**type**: `null` or `callable` **default**: `null`

When provided, this callable will be invoked to read the value from the underlying object that will be used within the column.
This disables the usage of the [PropertyAccessor](https://symfony.com/doc/current/components/property_access.html), described in the [property_path](#propertypath) option.

Value returned from given callable will be passed to every other callable option.

```php
$builder
    ->addColumn('seller', TextType::class, [
        'getter' => fn (Product $product) => $product->getSeller()->getUser(), // Returns an instance of User
        'formatter' => fn (User $user) => $user->getName(), // User returned in "getter" option is passed here
    ])
;
```

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

### `formatter`

**type**: `null` or `callable` **default**: `null`

Formats the value to the desired string.

The value passed as the argument can come either from the `value` option, 
or the property accessor after the extraction using the `property_path` option.

```php
$builder
    ->addColumn('name', TextType::class, [
        'formatter' => 'trim',    
    ])
    ->addColumn('quantity', NumberType::class, [
        'formatter' => fn (float $value) => number_format($value, 2) . 'kg',
    ])
;
```

### `export`

**type**: `bool` or `array` **default**: `[]` with some exceptions on built-in types (e.g. [ActionsType](actions.md))

Determines whether the column should be included in the exports.

This option accepts an array of options available for the column type.
It is used to differentiate options for regular rendering, and excel rendering.

For example, if you wish to display quantity column with "Quantity" label, but export with a "Qty" header:

```php
$columns
    ->add('quantity', NumberType::class, [
        'label' => 'Quantity',
        'translation_domain' => 'product',
        'export' => [
            'label' => 'Qty',
            // rest of the options are inherited, therefore "translation_domain" equals "product", etc.
        ],
    ])
;
```

Rest of the options are inherited from the column options.

Setting this option to `true` automatically copies the column options as the export column options.  
Setting this option to `false` excludes the column from the exports.

### `non_resolvable_options`

**type**: `array` **default**: `[]`

Because some column options can be an instance of `\Closure`, the bundle will automatically
call them, passing column value, data, whole column object and array of options, as the closure arguments.

This process is called "resolving", and the [formatter](#formatter) option is excluded from the process.
Because it may be possible, that the user does **not** want to get an option resolved (not call the closure at all),
it is possible to pass the option name to this array, to exclude it from the resolving process.

For example:

```php
$columns
    ->add('id', CustomType::class, [
        'uniqid' => fn (string $prefix) => uniqid($prefix),
        'non_resolvable_options' => [
            'uniqid',
        ],
    ])
;
```

The `uniqid` option will be available in the column views as a callable. For example, in templates:

```twig
{% block kreyu_data_table_column_custom %}
    {{ value }} ({{ uniqid('product_') }})
{% endblock %}
```
