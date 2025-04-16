<script setup>
    import ColumnTypeOptions from "./options/column.md";
</script>

# CollectionColumnType

The [`CollectionColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/CollectionColumnType.php) represents a column with value displayed as a list.

## Options

### `entry_type`

- **type**: `string`
- **default**: `'Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType'`

This is the column type for each item in this collection (e.g. [TextColumnType](text.md), [LinkColumnType](link.md), etc). 
For example, if you have an array of entities, you'd probably want to use the [LinkColumnType](link.md) to display them as links to their details view.

### `entry_options`

- **type**: `array`
- **default**: `[]`

This is the array that's passed to the column type specified in the [entry_type](#entry-type) option. 
For example, if you used the [LinkColumnType](link.md) as your `entry_type` option (e.g. for a collection of links of product tags), 
then you'd want to pass the href option to the underlying type:

```php
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

### `separator`

- **type**: `null` or `string`
- **default**: `', '`

Sets the value displayed between every item in the collection.

### `separator_html`

- **type**: `bool`
- **default**: `false`

If this option is true, the [separator](#separator) option will be displayed as HTML instead of text.
This is useful when using HTML elements (e.g. `<br>`) as a more modern visual separator.

Remember that exporting a collection with HTML as separator may result in HTML in cells.
You can always provide a different separator for exporting:

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionColumnType;

$builder
    ->addColumn('tags', CollectionColumnType::class, [
        'separator' => '<br/>',
        'separator_html' => true,
        'export' => [
            'separator' => ', ',
            'separator_html' => false, // May not be needed at all. This depends on the exporter.      
        ],
    ])
;
```

## Inherited options

<ColumnTypeOptions/>
