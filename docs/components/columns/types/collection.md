---
label: Collection
visibility: hidden
order: g
---

# Collection column type

The `CollectionColumnType` represents a column with value displayed as a list.

+-------------+---------------------------------------------------------------------+
| Parent type | [ColumnType](column)
+-------------+---------------------------------------------------------------------+
| Class       | [:icon-mark-github: CollectionColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/CollectionColumnType.php)
+-------------+---------------------------------------------------------------------+

## Options

### `entry_type`

- **type**: `string`
- **default**: `'Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType'`

This is the column type for each item in this collection (e.g. [TextColumnType](text), [LinkColumnType](link), etc). 
For example, if you have an array of entities, you'd probably want to use the [LinkColumnType](link) to display them as links to their details view.

### `entry_options`

- **type**: `array`
- **default**: `['property_path' => false]`

This is the array that's passed to the column type specified in the entry_type option. 
For example, if you used the [LinkColumnType](link) as your `entry_type` option (e.g. for a collection of links of product tags), 
then you'd want to pass the href option to the underlying type:

```php #
use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\LinkColumnType;

$builder
    ->addColumn('tags', CollectionColumnType::class, [
        'entry_type' => LinkColumnType::class,
        'entry_options' => [
            'href' => function (Tag $tag): string {
                return $this->urlGenerator->generate('tag_show', [
                    'id' => $tag->getId(),
                ]);
            },
            'formatter' => function (Tag $tag): string {
                return $tag->getName();
            },
        ],    
    ])
;
```

!!! Note
The options resolver normalizer ensures the `property_path` is always present in the `entry_options` array, and it defaults to `false`.
!!!

### `separator`

- **type**: `null` or `string`
- **default**: `','`

Sets the value displayed between every item in the collection.

## Inherited options

{{ include '_column_options' }}
