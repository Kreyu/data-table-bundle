---
order: b
---

# Creating data tables

To create a data table, either:

* use the trait to gain access to helpful methods;
* inject data table factory and use it directly;

For the sake of simplicity, the documentation uses the trait method:

```php # src/Controller/ProductController.php
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
}
```

The trait gives access to three helper methods:

| Method                   | Description                                                                                                                                                  |
|--------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `createDataTable`        | Creates data table using the type class.                                                                                                                     |
| `createNamedDataTable`   | Creates data table using the type classes, but explicitly sets its name. Used in cases where the single page displays multiple data tables of the same type. |
| `createDataTableBuilder` | Creates a builder to describe the data table manually, without type classes. In most cases it is used for prototyping rather than actual usage.              |

Therefore, to create a data table, we need to create a data table type class.

## Creating data table type classes

The data table type classes work as a blueprint. A single type can be used to create as many data tables as needed - making them a nice, reusable piece of code.
Those classes implement the [DataTableTypeInterface](), however, it is recommended to extend them from the [AbstractDataTableType](), which already implements the interface and provides some utilities.

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
}
```

Now, that the data table type class has been created, it can be used in the controller:

```php # src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index()
    {
        $dataTable = $this->createDataTable(ProductDataTableType::class);
    }
}
```

Running the code will result in an error:

> The data table has no proxy query. You must provide it using either the data table factory or the builder "setQuery()" method.

This is because we haven't passed anything the data table can work on.
Since we are using Doctrine ORM, the query builder should be passed as the "query" argument:

```php #14 src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(ProductRepository $repository)
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $repository->createQueryBuilder('product'),
        );
    }
}
```

Running the code again will result in yet another error:

> The data table has no configured columns. You must provide them using the builder "addColumn()" method.

The message is self-explanatory - the data table has no configured columns - it is time to [add some of those](adding-columns).
