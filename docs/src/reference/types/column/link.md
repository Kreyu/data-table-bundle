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

The callback will always receive three arguments:

- first represents a data of the column
- second represents a data of the row
- third is always an instance of the column

Let's assume, that we're displaying a list of products, and each product has one category:

```php
use App\Entity\Category;
use App\Entity\Product;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\LinkColumnType;

$builder
    ->addColumn('category', LinkColumnType::class, [
        'href' => function (Category $category, Product $product, ColumnInterface $column): string {
            return $this->urlGenerator->generate('product_category_show', [
                'product' => $product->getId(),
                'category' => $category->getId(),
            ]);
        },
    ])
;
```

### `target`

- **type**: `string` or `callable`
- **default**: `'_self'`

Sets the value that will be used as a [target attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-target). 
Similar to [`href`](#href) option, you can pass a callable that receives three arguments.

## Inherited options

<ColumnTypeOptions/>
