---
order: e
---

# Defining the filters

One of the most important features of the data tables is the ability to filter the data.
Similar to data tables and its columns, the filters are defined using the [type classes](../features/type-classes.md).

## Adding filters to the data table

Let's start by adding a filter for each field in the product entity:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Columns added before...
        
        $builder
            ->addFilter('id', NumericFilterType::class)
            ->addFilter('name', StringFilterType::class)
            ->addFilter('createdAt', DateRangeFilterType::class)
        ;
    }
}
```

The builder's `addFilter()` method accepts _three_ arguments:

- filter name — which in most cases will represent a property path in the underlying entity;
- filter type — with a fully qualified class name;
- filter options — defined by the filter type, used to configure the filter;

For reference, see [built-in filter types](../reference/filters/types.md).

Being on the subject of filters, let's continue by enabling a [global search feature](enabling-global-search.md).
