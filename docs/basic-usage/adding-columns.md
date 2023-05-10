---
order: c
---

# Adding columns

The data table builder object can be used to describe the columns used in the table.  
Similar to data tables, the columns are defined using the [type classes](../philosophy/understanding-the-types-api.md).

## Adding columns to the data table

Let's start by adding a column for each field in the product entity:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('id', NumberColumnType::class)
            ->addColumn('name', TextColumnType::class)
            ->addColumn('createdAt', DateTimeColumnType::class)
        ;
    }
}
```

The builder's `addColumn()` method accepts _three_ arguments:

- column name - which in most cases will represent a property path in the underlying entity 
- column type - with a fully qualified class name
- column options - defined by the column type, used to configure the column  

For reference, see [built-in column types](../components/columns/types.md).

## Making the columns sortable

The column types are customizable using the options array. The options can be passed as the third argument of the builder's `addColumn()` method. By default, columns are **not** sortable, because their `sort` option equals `false`. To change that, set the option to `true`:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('id', NumberColumnType::class, [
                'sort' => true,
            ])
            ->addColumn('name', TextColumnType::class)
            ->addColumn('createdAt', DateTimeColumnType::class)
        ;
    }
}
```

Now that the data table contains some columns, let's [render it](../usage/rendering-the-table.md) to the user.
