# CallbackType (Doctrine ORM)

The [CallbackType](#) represents a filter that allows manual application of complex conditions to the query. 

## Supported operators

Supports all operators, but it doesn't affect the actual query.

## Options

### `callback`

**type**: `callable`

Sets a callable that operates on the query passed as a first argument:

```php
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\CallbackFilter;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;

$builder
    ->addFilter('quantity', CallbackFilter::class, [
        'callback' => function (ProxyQueryInterface $query, FilterData $data, FilterInterface $filter) use ($user): void {
            $alias = current($query->getRootAliases());
            
            if ($user->isDistributor()) {
                $query->andWhere($query->expr()->gte("$alias.quantity", 0));
            }
            
            // Remember to use parameters to prevent SQL Injection!
            // To help with that, Doctrine ORM's ProxyQueryInterface has special method "getUniqueParameterId",
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

**Hint**: if same definition of `CallbackType` is used multiple times, consider creating custom filter class, to reduce repeated code (see [creating custom Doctrine ORM filter](../../types-reference.md#creating-custom-filter-class--doctrine-orm-)).

## Inherited options

See [base filter type documentation](../../filter.md).

## Overridden options

### `field_type`

**type**: `string` **default**: `'Symfony\Bridge\Doctrine\Form\Type\EntityType'`
