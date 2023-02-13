# How to Create a Custom Filter Type

This bundle comes with multiple filter types, all of which are integrating with the [Doctrine ORM](https://github.com/doctrine/orm).

## Creating Doctrine ORM Filter Types

To create a custom Doctrine ORM filter type, create a class that extends from the [Doctrine ORM abstract type](../../src/Bridge/Doctrine/Orm/Filter/Type/AbstractType.php):

```php
<?php

namespace App\DataTable\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\AbstractType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface;

class CustomType extends AbstractType
{
    protected function filter(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void
    {
        $alias = current($query->getRootAliases());
        
        // Remember to use parameters to prevent SQL Injection!
        // To help with that, Doctrine ORM's ProxyQueryInterface has special method "getUniqueParameterId",
        // that will generate a unique parameter name (inside its query context), handy!
        $parameter = $query->getUniqueParameterId(); 
        
        $query
            ->andWhere($query->expr()->eq("$alias.type"), ":$parameter")
            ->setParameter($parameter, $data->getValue())
        ;
    }
}
```

> ### 💡 Important note
> Note that there's a `filter()` method instead of a base `apply()` method!
> 
> The base [Doctrine ORM abstract type](../../src/Bridge/Doctrine/Orm/Filter/Type/AbstractType.php) ensures, that the received query
> is an instance of [Doctrine ORM's ProxyQueryInterface](../../src/Bridge/Doctrine/Orm/Query/ProxyQueryInterface.php), which has access to all the [QueryBuilder](https://www.doctrine-project.org/projects/doctrine-orm/en/2.14/reference/query-builder.html) methods, 
> and some additional helper methods, such as `generateUniqueParameterId()`.

## Creating Filter Types For Custom Proxy Query

If your application is not using Doctrine ORM, you can [create custom proxy query class](../create_custom_proxy_query_classes.md).
Then, you can create an abstract filter type class, that every other filter will extend.
You can use the same approach as the [Doctrine ORM ProxyQuery class](../../src/Bridge/Doctrine/Orm/Query/ProxyQueryInterface.php),
and add an abstract `filter()` method, and use the `apply()` to make sure a valid type is given.  

```php
<?php

namespace App\DataTable\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\AbstractType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface;
use App\DataTable\Query\CustomProxyQuery;

class CustomType extends AbstractType
{
    abstract protected function filter(CustomProxyQuery $query, FilterData $data, FilterInterface $filter): void;

    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void
    {
        if (!is_a($query, CustomProxyQuery::class)) {
            throw new UnexpectedTypeException($query, CustomProxyQuery::class);
        }

        $this->filter($query, $data, $filter);
    }
}
```
