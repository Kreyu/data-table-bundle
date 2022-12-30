# CollectionType

The [CollectionType](../../src/Column/Type/CollectionType.php) column represents a column with value displayed as a list of other column type.

**Note**: this type implements `ColumnFactoryAwareInterface`, which gives ability to use the column factory.

## Options

### `entry_type`

**type**: `string` **default**: `'Kreyu\Bundle\DataTableBundle\Column\Type\TextType'`

This is the column type for each item in this collection (e.g. [TextType](text.md), [LinkType](link.md), etc). 
For example, if you have an array of entities, you'd probably want to use the [LinkType](link.md) to display them as links to their details view. 

### `entry_options`

**type**: `array` **default**: `[]`

This is the array that's passed to the column type specified in the `entry_type` option. 
For example, if you used the [LinkType](link.md) as your `entry_type` option (e.g. for a collection of links of product tags), 
then you'd want to pass the `href` option to the underlying type:

```php
$columns
    ->add('tags', CollectionType::class, [
        'entry_type' => LinkType::class,
        'entry_options' => [
            'property_path' => false,
            'value' => function (Tag $tag): string {
                return $tag->getName(),
            },
            'href' => function (Tag $tag): string {
                return $this->urlGenerator->generate('tag_show', [
                    'id' => $tag->getId(),
                ]);
            },  
        ],    
    ])
;
```

### `prototype`

**type**: `null` or `ColumnInterface` **default**: column created from `entry_` options, or `null` if column factory does not exist in the type

This option holds a column created using the `entry_type` and `entry_options` options.  

## Inherited options

See [abstract column type documentation](abstract.md).
