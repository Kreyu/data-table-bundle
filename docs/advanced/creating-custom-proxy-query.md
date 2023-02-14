# Creating custom proxy query

This bundle comes with [Doctrine ORM Proxy Query](../src/Bridge/Doctrine/Orm/Query/ProxyQuery.php) built-in.

To create a custom request handler, create a class that implements [ProxyQueryInterface](../src/Query/ProxyQueryInterface.php):

```php
// src/DataTable/Query/CustomProxyQuery.php
namespace App\DataTable\Query;

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

To understand how to use those methods, take look at the built-in [Doctrine ORM Proxy Query](../src/Bridge/Doctrine/Orm/Query/ProxyQuery.php) class.
