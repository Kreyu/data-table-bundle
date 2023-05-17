### `label`

- **type**: `null`, `string` or `Symfony\Component\Translation\TranslatableMessage`
- **default**: `null` - the label is "guessed" from the column name

Sets the label that will be used when rendering the column header.

### `header_translation_domain`

- **type**: `false` or `string`
- **default**: `'KreyuDataTable'`

Sets the translation domain used when translating the column header.  
Setting the option to `false` disables its translation.

### `header_translation_parameters`

- **type**: `array`
- **default**: `[]`

Sets the parameters used when translating the column header.

### `value_translation_domain`

- **type**: `false` or `string`
- **default**: inherited from the data table translation domain

Sets the translation domain used when translating the column value.  
Setting the option to `false` disables its translation.

### `property_path`

- **type**: `null`, `false` or `string`
- **default**: `null` - the property path is "guessed" from the column name

Sets the property path used by the [PropertyAccessor](https://symfony.com/doc/current/components/property_access.html) to retrieve column value of each row.  
Setting the option to `false` disables property accessor.

```php #
$builder
    ->addColumn('category', TextColumnType::class, [
        'property_path' => 'category.name',
    ])
;
```

### `getter`

- **type**: `null` or `callable`
- **default**: `null`

When provided, this callable will be invoked to read the value from the underlying object that will be used within the column.
This disables the usage of the [PropertyAccessor](https://symfony.com/doc/current/components/property_access.html), described in the [property_path](#property_path) option.

```php #
$builder
    ->addColumn('category', TextColumnType::class, [
        'getter' => fn (Product $product) => $product->getCategory(),
    ])
;
```

### `sort`

- **type**: `bool` or `string`
- **default**: `false` - the sortable behavior is disabled

Sets the sort field used by the sortable behavior.

Setting the option to `true` enables column sorting and uses the column name as a sort field name.  
Setting the option to `false` disables column sorting.

### `block_prefix`

- **type**: `string`
- **default**: column type block prefix

Allows you to add a custom block prefix and override the block name used to render the column type.
Useful for example if you have multiple instances of the same column type, and you need to personalize 
the rendering of some of them, without the need to create a new column type.

### `formatter`

- **type**: `null` or `callable`
- **default**: `null`

Formats the value to the desired string.

```php #
$builder
    ->addColumn('quantity', NumberColumnType::class, [
        'formatter' => fn (float $value) => number_format($value, 2) . 'kg',
    ])
;
```

### `export`

- **type**: `bool` or `array`
- **default**: `[]`

This option accepts an array of options available for the column type.
It is used to differentiate options for regular rendering, and excel rendering.

For example, if you wish to display quantity column with "Quantity" label, but export with a "Qty" header:

```php #
$builder
    ->addColumn('quantity', NumberColumnType::class, [
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

### `header_attr`

- **type**: `array`
- **default**: `[]`

If you want to add extra attributes to an HTML column header representation (`<th>`) you can use the attr option.
It's an associative array with HTML attributes as keys.
This can be useful when you need to set a custom class for a column:

```php #
$builder
    ->addColumn('quantity', NumberColumnType::class, [
        'header_attr' => [
            'class' => 'text-end',
        ],
    ])
;
```

### `value_attr`

- **type**: `array`
- **default**: `[]`

If you want to add extra attributes to an HTML column value representation (`<td>`) you can use the attr option.
It's an associative array with HTML attributes as keys.
This can be useful when you need to set a custom class for a column:

```php #
$builder
    ->addColumn('quantity', NumberColumnType::class, [
        'value_attr' => [
            'class' => 'text-end',
        ],
    ])
;
```
