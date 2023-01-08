# AbstractType

Every column type should extend from [AbstractType](#), to inherit necessary options.

## Options

### `label`

**type**: `string` or `TranslatableMessage` **default**: the label is "guessed" from the column name

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

### `sort_field`

**type**: `false` or `string` **default**: `false` - the sortable behavior is disabled

Sets the sort field used by the sortable behavior.   
Setting the option to `false` disables column sorting.

### `block_name`

**type**: `string` **default**: `data_table_`  + column type block prefix (see [Knowing which block to customize](#))

Allows you to add a custom block name to the ones used by default to render the column type.
Useful for example if you have multiple instances of the same column type, and you need to personalize the rendering of the columns individually.

By default, if column type class name is `TextType`, the block name option will equal `data_table_text`.

### `block_prefix`

**type**: `string` **default**: column type block prefix (see [Knowing which block to customize](#))

Allows you to add a custom block prefix and override the block name used to render the column type.
Useful for example if you have multiple instances of the same column type, and you need to personalize the rendering of all of them without the need to create a new column type.

### `value`

**type**: `mixed` **default**: `null` - the value retrieved by the property accessor is used

Sets the column value of each row.  
Callable can be used to provide an option value based on an actual row value, which is passed as a first argument.

```php
$columns
    ->add('ean', TextType::class, [
        'value' => fn (string $value) => trim($value),
    ])
    ->add('quantity', TextType::class, [
        'value' => fn (float $value) => number_format($value, 2) . 'kg',
    ])
;
```

If you disabled property accessor by setting the `property_path` option to `false`, this is a way to retrieve a value manually:

```php
$columns
    ->add('fullName', TextType::class, [
        'property_path' => false,
        'value' => fn (User $value) => implode(' ', [$user->name, $user->surname]),    
    ])
;
```

Because property accessor is not called, the value passed as the first argument is a "raw" row value (and for most cases it will be an entity).

### `display_personalization_button`

**type**: `bool` **default**: `false`

If this value is true, a button will be visible next to the column header. Clicking it opens the personalization modal.
