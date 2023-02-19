# CallbackType

S|Requires Doctrine ORM||

The [CallbackType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/CallbackType.php) represents a filter 
that allows manual application of complex conditions to the query. 

## Supported operators

Supports all operators, but it doesn't affect the actual query.

## Options

### `callback`

**type**: `callable`

Sets a callable that operates on the query passed as a first argument:

```php
use Kreyu\Bundle\DataTable\Bridge\Doctrine\Orm\Filter\CallbackFilter;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

$builder
    ->addFilter('type', CallbackFilter::class, [
        /** @var DoctrineOrmProxyQuery $query */
        'callback' => function (ProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void {
            $alias = current($query->getRootAliases());
            
            // Remember to use parameters to prevent SQL Injection!
            // To help with that, DoctrineOrmProxyQuery has special method "getUniqueParameterId",
            // that will generate a unique parameter name (inside its query context), handy!
            $parameter = $query->getUniqueParameterId(); 
            
            $query
                ->andWhere($query->expr()->eq("$alias.type"), ":$parameter")
                ->setParameter($parameter, $data->getValue())
            ;
            
            // ...
        } 
    ])
```

## Inherited options

See [base filter type documentation](https://github.com/Kreyu/data-table-bundle/blob/main/docs/filter/types/filter.md).

## Overridden options

### `field_type`

**type**: `string` **default**: `'Symfony\Bridge\Doctrine\Form\Type\EntityType'`
