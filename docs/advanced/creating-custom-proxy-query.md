# Creating custom proxy query

This bundle comes with [DoctrineOrmProxyQuery](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Query/DoctrineOrmProxyQuery.php) built-in.

To create a custom request handler, create a class that implements [ProxyQueryInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Query/ProxyQueryInterface.php):

```php
// src/DataTable/Query/CustomProxyQuery.php
namespace App\DataTable\Query;

use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

class CustomProxyQuery implements ProxyQueryInterface
{
    public function sort(SortingData $sortingData): void
    {
        // ...
    }

    public function paginate(PaginationData $paginationData): void
    {
        // ...
    }

    public function getPagination(): PaginationInterface
    {
        // ...
    }
}
```

The current page results should be container within the pagination class. 
Take look at the built-in [DoctrineOrmProxyQuery](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Query/DoctrineOrmProxyQuery.php) class.

## Creating the proxy query factory

When using the data table factory, you can pass either the custom proxy query class, or just a data you want to operate on.
For example, if you pass Doctrine ORM's QueryBuilder class, it will be automatically converted to the [DoctrineOrmProxyQuery](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Query/DoctrineOrmProxyQuery.php) object:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProductType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQuery;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        $query = $repository->createQueryBuilder('product');

        // Option 1: pass desired ProxyQuery manually:
        $dataTable = $this->createDataTable(ProductType::class, new DoctrineOrmProxyQuery($query));
        
        // Option 2: pass the query builder directly, and let the bundle do the work:
        $dataTable = $this->createDataTable(ProductType::class, $query);
        
        // ...
    }
}
```

This is thanks to the proxy query factories. In the background, a [ChainProxyQueryFactory](https://github.com/Kreyu/data-table-bundle/blob/main/src/Query/ChainProxyQueryFactory.php) is used, which iterates
on every proxy query factory registered in the container, and returns the first successfully created proxy query.

To create a custom request handler, create a class that implements [ProxyQueryFactoryInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Query/ProxyQueryFactoryInterface.php):

```php
// src/DataTable/Query/CustomProxyQueryFactory.php
namespace App\DataTable\Query;

use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

class CustomProxyQueryFactory implements ProxyQueryFactoryInterface
{
    public function create(mixed $data): ProxyQueryInterface
    {
        if ($data instanceof CustomDataSourceObject) {
            return new CustomProxyQuery($data);        
        }

        throw new UnexpectedTypeException($data, CustomProxyQuery::class);
    }
}
```

Note that if the custom proxy query does not support a specific data class, you have to throw an [UnexpectedTypeException](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exception/UnexpectedTypeException.php),
so the chain proxy query factory will know to skip that factory and check other ones.

## Registering the proxy query factory as a service

Proxy query factories must be registered as services and tagged with the `kreyu_data_table.column.proxy_query.factory` tag.
If you're using the [default services.yaml configuration](https://symfony.com/doc/current/service_container.html#service-container-services-load-example),
this is already done for you, thanks to [autoconfiguration](https://symfony.com/doc/current/service_container.html#services-autoconfigure).
