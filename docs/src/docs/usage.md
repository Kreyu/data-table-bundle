# Usage

[[toc]]

## Creating data tables

Data tables are defined using a _type classes_. Those classes implement [DataTableTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/DataTableTypeInterface.php), although, it is recommended to extend from the [AbstractDataTableType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/AbstractDataTableType.php) class:

```php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Define column, filters, actions, exporters, etc...
    }
}
```

<div class="tip custom-block" style="padding-top: 8px;">

Recommended namespace for the column type classes is `App\DataTable\Type\`.

</div>

From here, you can add [columns](components/columns.md), [filters](components/filters.md), [actions](components/actions.md) and [exporters](components/exporters.md).

In most cases, the data tables are created in the controller, using the `createDataTable()` method from the `DataTableFactoryAwareTrait`.

```php
use App\Repository\ProductRepository;
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait; // [!code highlight]
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait; // [!code highlight]

    public function index(ProductRepository $productRepository)
    {
        $queryBuilder = $productRepository->createQueryBuilder('product');

        $dataTable = $this->createDataTable(ProductDataTableType::class, $queryBuilder);
    }
}
```

This method accepts _three_ arguments:

- type — with a fully qualified class name;
- data — in most cases, an instance of Doctrine ORM query builder;
- options — defined by the data table type, used to configure the data table;

In above example, we're passing an instance of Doctrine ORM query builder as data, not results.
This allows the bundle to paginate the results, apply filtration, and more.

## Handling the request

In order to be able to paginate, sort, filter, personalize or export the data table, call the `handleRequest()` method of the data table:

```php
use App\Repository\ProductRepository;
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(ProductRepository $productRepository) // [!code --]
    public function index(Request $request, ProductRepository $productRepository) // [!code ++]
    {
        $queryBuilder = $productRepository->createQueryBuilder('product');

        $dataTable = $this->createDataTable(ProductDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request); // [!code ++]
    }
}
```

This method calls the [request handler](./features/extensibility.md#request-handlers) to handle all the hard work.

## Rendering the data tables

In order to render the data table, create the data table view and pass it to the template:

```php
use App\Repository\ProductRepository;
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(Request $request, ProductRepository $productRepository)
    {
        $queryBuilder = $productRepository->createQueryBuilder('product');

        $dataTable = $this->createDataTable(ProductDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request);
        
        return $this->render('product/index.html.twig', [ // [!code ++]
            'products' => $dataTable->createView(), // [!code ++]
        ]); // [!code ++]
    }
}
```

:::tip Don't forget to call the `createView()` method!
This creates a `DataTableView` object, used to render the data table.
:::

Now, in the template, render the data table using the `data_table` function:

```twig
{# templates/product/index.html.twig #}

<div class="card">
    {{ data_table(products) }}
</div>
```

By default, the data table will look somewhat _ugly_, because we haven't configured the theme yet - see [theming](features/theming.md) documentation section.

## Using array as data source

:::warning In most cases, using array as data source is used only for fast prototyping!
Remember that paginating an array is not memory efficient, as every item is already loaded into memory.
If your data comes from a database, pass an instance of Doctrine ORM query builder instead.
:::

In some cases, you might want to use an array as a data source. This can be achieved by simply passing an array as data to the data table factory method:

```php
use App\Entity\Product;
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index()
    {
        $products = [
            new Product(id: 1, name: 'Product 1'),
            new Product(id: 2, name: 'Product 2'),
            new Product(id: 3, name: 'Product 3'),
        ];

        $dataTable = $this->createDataTable(ProductDataTableType::class, $products);
    }
}
```

Alternatively, you can manually create an instance of `ArrayProxyQuery` to provide total item count different from given array count.
This can be useful in cases where you're already retrieving paginated data and still want the data table to properly display the pagination controls:

```php
use App\Entity\Product;
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Query\ArrayProxyQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index()
    {
        $products = new ArrayProxyQuery(
            data: [
                new Product(id: 1, name: 'Product 1'),
                new Product(id: 2, name: 'Product 2'),
                new Product(id: 3, name: 'Product 3'),
            ], 
            totalItemCount: 25,
        );  
        
        $dataTable = $this->createDataTable(ProductDataTableType::class, $products);
        
        // For example, in this case, paginating with 3 items per page will result in 9 pages,
        // because the proxy query now assumes there's 25 items in total, and the data array
        // only represents results of a currently displayed page.
        $dataTable->paginate(new PaginationData(page: 1, perPage: 3));
    }
}
```

Sorting will perform `usort` on the given array, while paginating will simply slice the array.
However, **there are no built-in filters** for this proxy query, but you can implement 
your own filter types - see [creating filter types](components/filters#creating-filter-types).

