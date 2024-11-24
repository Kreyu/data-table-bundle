<script setup>
const props = defineProps({
    defaults: {
        type: Object,
        default: {
            label: 'null',
            property_path: 'null',
        },
    },
    excludedOptions: {
        type: Array,
        default: ['test'],
    },
})
</script>

### `label`

- **type**: `null`, `string` or `Symfony\Component\Translation\TranslatableInterface`
- **default**: `{{ defaults.label }}` 

Sets the label that will be used in column header and personalization column list.

When value equals `null`, a sentence cased column name is used as a label, for example:

| Column name | Guessed label |
|-------------|---------------|
| name        | Name          |
| firstName   | First name    |

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

### `value_translation_parameters`

Sets the parameters used when translating the column value.

If given the callable, it will receive two arguments:
- column value, e.g. column (row) data formatted by the optional `formatter` option;
- column (row) data, e.g. value returned by property accessor or getter;

```php
// Assume the data table of User entities
$builder->addColumn('firstName', options: [
    'value_translation_parameters' => function (string $firstName, User $user) {
        return [...];
    },
]);
```

The `ColumnValueView` will contain the resolved callable.

### `property_path`

- **type**: `null`, `false` or `string`
- **default**: `{{ defaults.property_path }}`

Sets the property path used by the [PropertyAccessor](https://symfony.com/doc/current/components/property_access.html) to retrieve column value of each row.  
Setting the option to `false` disables property accessor.

```php
$builder
    ->addColumn('category', TextColumnType::class, [
        'property_path' => 'category.name',
    ])
;
```

When value equals `null`, the column name is used as a property path.

### `getter`

- **type**: `null` or `callable`
- **default**: `null`

When provided, this callable will be invoked to read the value from the underlying object that will be used within the column.
This disables the usage of the [PropertyAccessor](https://symfony.com/doc/current/components/property_access.html), described in the [property_path](#property_path) option.

```php
$builder
    ->addColumn('category', TextColumnType::class, [
        'getter' => fn (Product $product) => $product->getCategory(),
    ])
;
```

### `sort`

- **type**: `bool` or `string`
- **default**: `false` - the sortable behavior is disabled

Sets the sort field used by the sortable behavior. Setting the option to:
- `true` - enables column sorting and uses the column name as a sort field name;
- `false` - disables column sorting;
- string - defines sort property path;

### `block_prefix`

- **type**: `string`
- **default**: value returned by the column type `getBlockPrefix()` method

Allows you to add a custom block prefix and override the block name used to render the column type.
Useful for example if you have multiple instances of the same column type, and you need to personalize
the rendering of some of them, without the need to create a new column type.

<span v-if="!excludedOptions.includes('formatter')">

### `formatter`

- **type**: `null` or `callable`
- **default**: `null`

Formats the value to the desired string.

```php
$builder
    ->addColumn('quantity', NumberColumnType::class, [
        'formatter' => function (float $value, Product $product, ColumnInterface $column, array $options) {
            return number_format($value, 2) . $product->getUnit();
        },
    ])
;
```

</span>

### `export`

- **type**: `bool` or `array`
- **default**: `[]`

This option accepts an array of options available for the column type.
It is used to differentiate options for regular rendering, and excel rendering.

For example, if you wish to display quantity column with "Quantity" label, but export with a "Qty" header:

```php
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

```php
$builder
    ->addColumn('quantity', NumberColumnType::class, [
        'header_attr' => [
            'class' => 'text-end',
        ],
    ])
;
```

### `value_attr`

- **type**: `array` or `callable`
- **default**: `[]`

If you want to add extra attributes to an HTML column value representation (`<td>`) you can use the attr option.
It's an associative array with HTML attributes as keys.
This can be useful when you need to set a custom class for a column:

```php
$builder
    ->addColumn('quantity', NumberColumnType::class, [
        'value_attr' => [
            'class' => 'text-end',
        ],
    ])
;
```

You can pass a `callable` to perform a dynamic attribute generation:

```php
$builder
    ->addColumn('quantity', NumberColumnType::class, [
        'value_attr' => function (int $quantity, Product $product) {
            return [
                'class' => $quantity === 0 && !$product->isDisabled() ? 'text-danger' : '',
            ],
        },
    ])
;
``` 

### `priority`

- **type**: `integer`
- **default**: `0`

Columns are rendered in the same order as they are included in the data table.
This option changes the column rendering priority, allowing you to display columns earlier or later than their original order.

The higher this priority, the earlier the column will be rendered.
Priority can albo be negative and columns with the same priority will keep their original order.

**Note**: column priority can be changed by the [personalization feature](../../../../docs/features/personalization.md).

### `visible`

- **type**: `bool`
- **default**: `true`

Determines whether the column is visible to the user.

**Note**: column visibility can be changed by the [personalization feature](../../../../docs/features/personalization.md).

### `personalizable`

- **type**: `bool`
- **default**: `true`

Determines whether the column is personalizable.
The non-personalizable columns are not modifiable by the [personalization feature](../../../../docs/features/personalization.md).
