---
order: i
---

# Proxy queries

This bundle is data source agnostic, meaning it is not tied to any specific ORM, such as Doctrine ORM.
This is accomplished thanks to **proxy queries**, which work as an adapter for the specific data source.

For example, if you want to display a list of products from the database, and your application uses Doctrine ORM,
then you'd want to use the built-in [:icon-mark-github: DoctrineOrmProxyQuery](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Query/DoctrineOrmProxyQuery.php).
If your data comes from another source (from an array, from CSV, etc.), then you can create a custom proxy query class.

## Creating custom proxy query

To create a custom proxy query, create a class that implements [:icon-mark-github: ProxyQueryInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Query/ProxyQueryInterface.php):

```php # src/DataTable/Query/ArrayProxyQuery.php
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

class ArrayProxyQuery implements ProxyQueryInterface
{
    public function __construct(
        private array $data,
    ) {
    }
    
    public function sort(SortingData $sortingData): void
    {
        // sort the data, for example, using the uksort...
    }

    public function paginate(PaginationData $paginationData): void
    {
        // save pagination data in proxy query...
    }

    public function getPagination(): PaginationInterface
    {
        // create new pagination using the limit iterator as items...
    }
}
```

!!!
The recommended namespace for the proxy queries is `App\DataTable\Query`.
!!!

Now you can use the custom proxy query when creating the data tables:

```php #20 src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use App\DataTable\Query\ArrayProxyQuery;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index()
    {
        $products = [
            new Product(name: 'Product #1'),
            new Product(name: 'Product #2'),
            new Product(name: 'Product #3'),
        ];
        
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: new ArrayProxyQuery($products),
        );
    }
}
```

## Creating proxy query factory

When using the data table factory, you can pass either the custom proxy query class, or just the data you want to operate on.
For example, if you pass Doctrine ORM's QueryBuilder class, it will be automatically converted to the [:icon-mark-github: DoctrineOrmProxyQuery](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Query/DoctrineOrmProxyQuery.php) object:

```php #15-16 src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use App\DataTable\Query\ArrayProxyQuery;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(ProductRepository $repository)
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            // note that there's no DoctrineOrmProxyQuery, just a QueryBuilder:
            query: $repository->createQueryBuilder('product'),
        );
    }
}
```

If you try to do the same with the custom proxy query, it will result in an error:

> Unable to create ProxyQuery for given data

In the background, the [:icon-mark-github: ChainProxyQueryFactory](https://github.com/Kreyu/data-table-bundle/blob/main/src/Query/ChainProxyQueryFactory.php)
iterates through registered proxy query factories, and returns the first successfully created proxy query.
The error occurs because there is no factory to create the custom type.

To create a proxy query factory, create a class that implements the [:icon-mark-github: ProxyQueryFactoryInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Query/ProxyQueryFactoryInterface.php):

```php # src/DataTable/Query/ArrayProxyQueryFactory.php
use App\DataTable\Query\ArrayProxyQuery;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

class ArrayProxyQueryFactory implements ProxyQueryFactoryInterface
{
    public function create(mixed $data): ProxyQueryInterface
    {
        if (!is_array($data)) {
            throw new UnexpectedTypeException($data, ArrayProxyQuery::class);        
        }

        return new ArrayProxyQuery($data);
    }
}
```

If the custom proxy query does not support a specific data class, the factory **have** to throw an [:icon-mark-github: UnexpectedTypeException](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exception/UnexpectedTypeException.php),
so the chain proxy query factory will know to skip that factory and check other ones.

Proxy query factories must be registered as services and tagged with the `kreyu_data_table.proxy_query.factory` tag.
If you're using the [default services.yaml configuration](https://symfony.com/doc/current/service_container.html#service-container-services-load-example),
this is already done for you, thanks to [autoconfiguration](https://symfony.com/doc/current/service_container.html#services-autoconfigure).

In above examples, now it would be possible to pass the array directly as the "query" argument:

```php #19 src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index()
    {
        $products = [
            new Product(name: 'Product #1'),
            new Product(name: 'Product #2'),
            new Product(name: 'Product #3'),
        ];
        
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $products,
        );
    }
}
```
