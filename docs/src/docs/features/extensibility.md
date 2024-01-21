# Extensibility

There are multiple concepts that can be modified for a specific case.

[[toc]]

## Request handlers

The data tables by default have no clue about the requests.
To solve this problem, a request can be handled by the data table using the `handleRequest()` method.
This means an underlying request handler will be called, extracting the required data from the request,
and calling methods such as `sort()` or `paginate()` on the data table.

### Built-in request handlers

This bundle comes with [HttpFoundationRequestHandler](https://github.com/Kreyu/data-table-bundle/blob/main/src/Request/HttpFoundationRequestHandler.php),
which supports the [request object](https://github.com/symfony/http-foundation/blob/6.4/Request.php) common for the Symfony applications:

```php
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
        $dataTable->handleRequest($request);
    }
}
```

### Creating request handlers

To create a request handler, create a class that implements [RequestHandlerInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Request/RequestHandlerInterface.php):

```php
use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingField;

class CustomRequestHandler implements RequestHandlerInterface
{
    public function handle(DataTableInterface $dataTable, mixed $request = null): void
    {
        // Call desired methods with arguments based on the data from $request
        $dataTable->paginate(...);
        $dataTable->sort(...);
        $dataTable->personalize(...);
        $dataTable->filter(...);
        $dataTable->export(...); 
    }
}
```

<div class="tip custom-block" style="padding-top: 8px;">

The recommended namespace for the request handlers is `App\DataTable\Request`.

</div>

You can apply this request handler globally using the configuration file, or use `request_handler` option:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    # this should be a service id - which is class by default
    request_handler: 'App\DataTable\Request\CustomRequestHandler'
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    // this should be a service id - which is class by default
    $defaults->requestHandler('App\DataTable\Request\CustomRequestHandler');
};
```

```php [For data table type]
use App\DataTable\Request\CustomRequestHandler;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function __construct(
        private CustomRequestHandler $requestHandler,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'request_handler' => $this->requestHandler,
        ]);
    }
}
```

```php [For specific data table]
use App\DataTable\Request\CustomRequestHandler;
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function __construct(
        private CustomRequestHandler $requestHandler,
    ) {
    }
    
    public function index()
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $query,
            options: [
                'request_handler' => $this->requestHandler,
            ],
        );
    }
}
```
:::

## Proxy queries

This bundle is data source agnostic, meaning it is not tied to any specific ORM, such as Doctrine ORM.
This is accomplished thanks to **proxy queries**, which work as an adapter for the specific data source.

### Creating custom proxy query

To create a custom proxy query, create a class that implements [ProxyQueryInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Query/ProxyQueryInterface.php):

```php
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ResultSetInterface;

class ArrayProxyQuery implements ProxyQueryInterface
{
    public function __construct(
        private array $data,
    ) {
    }
    
    public function sort(SortingData $sortingData): void
    {
    }

    public function paginate(PaginationData $paginationData): void
    {
    }

    public function getResult(): ResultSetInterface
    {
    }
}
```

<div class="tip custom-block" style="padding-top: 8px;">

The recommended namespace for the proxy queries is `App\DataTable\Query`.

</div>

Now you can use the custom proxy query when creating the data tables:

```php
use App\DataTable\Type\ProductDataTableType;
use App\DataTable\Query\ArrayProxyQuery;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index()
    {
        // Note: the products are an instance of ArrayProxyQuery 
        $products = new ArrayProxyQuery([
            new Product(name: 'Product #1'),
            new Product(name: 'Product #2'),
            new Product(name: 'Product #3'),
        ]);
        
        $dataTable = $this->createDataTable(ProductDataTableType::class, $products); 
    }
}
```

### Creating proxy query factory

Each proxy query should have a factory, so the bundle can handle passing the raw data like so:

```php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index()
    {
        // Note: products are just a simple array, ArrayProxyQuery is not required
        $products = [
            new Product(name: 'Product #1'),
            new Product(name: 'Product #2'),
            new Product(name: 'Product #3'),
        ];
        
        $dataTable = $this->createDataTable(ProductDataTableType::class, $products);
    }
}
```

Without dedicated proxy query factory to handle array data, the bundle will throw an exception:

> Unable to create ProxyQuery for given data

In the background, the [ChainProxyQueryFactory](https://github.com/Kreyu/data-table-bundle/blob/main/src/Query/ChainProxyQueryFactory.php)
iterates through registered proxy query factories, and returns the first successfully created proxy query.
The error occurs because there is no factory to create the custom type.

To create a proxy query factory, create a class that implements the [ProxyQueryFactoryInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Query/ProxyQueryFactoryInterface.php):

```php
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

<div class="tip custom-block" style="padding-top: 8px;">

The recommended namespace for the proxy query factories is `App\DataTable\Query`.

</div>

If the custom proxy query does not support a specific data class, the factory **have** to throw an [UnexpectedTypeException](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exception/UnexpectedTypeException.php),
so the chain proxy query factory will know to skip that factory and check other ones.

Proxy query factories must be registered as services and tagged with the `kreyu_data_table.proxy_query.factory` tag.
If you're using the [default services.yaml configuration](https://symfony.com/doc/current/service_container.html#service-container-services-load-example),
this is already done for you, thanks to [autoconfiguration](https://symfony.com/doc/current/service_container.html#services-autoconfigure).
