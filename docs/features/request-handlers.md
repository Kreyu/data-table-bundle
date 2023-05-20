---
order: i
---

# Request handlers

The data tables by default have no clue about the requests.
To solve this problem, a request can be handled by the data table using the `handleRequest()` method.
This means an underlying request handler will be called, extracting the required data from the request,
and calling methods such as `sort()` or `paginate()` on the data table. 

## Built-in request handlers

This bundle comes with [:icon-mark-github: HttpFoundationRequestHandler](https://github.com/Kreyu/data-table-bundle/blob/main/src/Request/HttpFoundationRequestHandler.php),
which supports the [:icon-mark-github: request object](https://github.com/symfony/http-foundation/blob/6.2/Request.php) common for the Symfony applications:

```php #4,13 src/Controller/ProductController.php
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

## Creating request handlers

To create a request handler, create a class that implements [:icon-mark-github: RequestHandlerInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Request/RequestHandlerInterface.php):

```php # src/DataTable/Request/CustomRequestHandler.php
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

!!!
The recommended namespace for the request handlers is `App\DataTable\Request`.
!!!

You can apply this request handler globally using the configuration file, or use `request_handler` option:

+++ Globally (YAML)
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    # this should be a service id - which is class by default
    request_handler: 'App\DataTable\Request\CustomRequestHandler'
```
+++ Globally (PHP)
```php # config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    // this should be a service id - which is class by default
    $defaults->requestHandler('App\DataTable\Request\CustomRequestHandler');
};
```
+++ For data table type
```php # src/DataTable/Type/ProductDataTable.php
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
+++ For specific data table
```php # src/Controller/ProductController.php
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
+++
