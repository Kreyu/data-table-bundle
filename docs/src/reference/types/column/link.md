<script setup>
    import ColumnTypeOptions from "./options/column.md";
</script>

# LinkColumnType

The [`LinkColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/LinkColumnType.php) represents a column with value displayed as a link.

## Options

### `href`

- **type**: `string` or `callable`
- **default**: `'#'`

Sets the value that will be used as a [href attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-href).

```php
use App\Entity\Category;
use Kreyu\Bundle\DataTableBundle\Column\Type\LinkColumnType;

$builder
    ->addColumn('category', LinkColumnType::class, [
        'href' => function (Category $category): string {
            return $this->urlGenerator->generate('category_show', [
                'id' => $category->getId(),
            ]);
        },
    ])
;
```

### `target`

- **type**: `string` or `callable`
- **default**: `'_self'`

Sets the value that will be used as a [target attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-target).

## Inherited options

<ColumnTypeOptions/>
