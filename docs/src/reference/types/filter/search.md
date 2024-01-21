<script setup>
    import FilterTypeOptions from "./options/filter.md";
</script>

# SearchFilterType

The [`SearchFilterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/SearchFilterType.php) represents a special filter that is rendered on the outside of filtering form as a search input.

![Search filter type](/search_filter_type.png)

## Adding the search handler

Instead of using this filter, you can use the `setSearchHandler()` method:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->setSearchHandler(function (ProxyQueryInterface $query, string $search) {
            // ...
        });
    }
}
```

Defining a search handler automatically adds search filter. 

To disable this behavior, use the `setAutoAddingSearchFilter()` method: 

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->setAutoAddingSearchFilter(false);
    }
}
```

To override configuration of the automatically added filter, you can add search filter manually with the same name:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Set "__search" as filter name or use constant:
        $builder->addFilter(DataTableBuilderInterface::SEARCH_FILTER_NAME, SearchFilterType::class, [
            // ...
        ]);
    }
}
```

## Options

### `handler`

- **type**: `callable`

Sets callable that operates on the query passed as a first argument:

```php
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

$builder
    ->addFilter('search', SearchFilterType::class, [
        'handler' => function (ProxyQueryInterface $query, string $search): void {
            // ...
        },
    ])
```

## Inherited options

<FilterTypeOptions />
