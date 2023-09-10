# Columns

Columns are the main building blocks of a data tables, split into two parts: the header and the value itself.

## Adding columns

To add column, use data table builder's `addColumn()` method:

```php #12-14 src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;

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

The same method can also be used on already created data tables:

```php #17-19 src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function index()
    {
        $dataTable = $this->createDataTable(ProductDataTableType::class);
        
        $dataTable
            ->addColumn('id', NumberColumnType::class)
            ->addColumn('name', TextColumnType::class)
            ->addColumn('createdAt', DateTimeColumnType::class)
        ;
    }
}
```

This method accepts _three_ arguments:

- column name;
- column type — with a fully qualified class name;
- column options — defined by the column type, used to configure the column;

For reference, see [built-in column types](../../reference/columns/types.md).

## Removing columns

To remove existing column, use the builder's `removeColumn()` method:

```php #8 src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->removeColumn('id');
    }
}
```

The same method can also be used on already created data tables:

```php #16 src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function index()
    {
        $dataTable = $this->createDataTable(ProductDataTableType::class);
        
        $dataTable->removeColumn('id');
    }
}
```

Any attempt of removing the non-existent column will silently fail.

## Retrieving columns

To retrieve already defined global columns, use the builder's `getColumns()` or `getColumn()` method:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // retrieve all previously defined columns:
        $columns = $builder->getColumns();
        
        // or specific column:
        $column = $builder->getColumn('id');
        
        // or simply check whether the column is defined:
        if ($builder->hasColumn('id')) {
            // ...
        }
    }
}
```

The same methods are accessible on already created data tables:

```php # src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(Request $request)
    {
        $dataTable = $this->createDataTable(ProductDataTableType::class);
        
        // retrieve all previously defined columns:
        $columns = $dataTable->getColumns();
        
        // or specific column:
        $column = $dataTable->getColumn('id');
        
        // or simply check whether the column is defined:
        if ($dataTable->hasColumn('id')) {
            // ...
        }
    }
}
```

!!!warning Warning
Any attempt of retrieving a non-existent column will result in an `OutOfBoundsException`.  
To check whether the global column of given name exists, use the `hasColumn()` method.
!!!

!!!danger Important
Within the data table builder, the columns are still in their build state!
Therefore, columns retrieved by the methods:

- `DataTableBuilderInterface::getColumns()`
- `DataTableBuilderInterface::getColumn(string $name)`

...are instance of `ColumnBuilderInterface`, whereas methods:

- `DataTableInterface::getColumns()`
- `DataTableInterface::getColumn(string $name)`

...return instances of `ColumnInterface` instead.
!!!
