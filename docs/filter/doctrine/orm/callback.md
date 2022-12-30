# CallbackFilter (Doctrine ORM)

The [CallbackFilter](../../../../src/Bridge/Doctrine/Orm/Filter/CallbackFilter.php) represents a filter that allows manual application of complex conditions to the query. 

## Supported operators

Supports all operators, but it doesn't affect the actual query.

## Options

### `callback`

**type**: `callable`

Sets a callable that operates on the query passed as a first argument:

```php
$filters
    ->add('quantity', CallbackFilter::class, [
        'callback' => function (ProxyQueryInterface $query) use ($user): void {
            $alias = current($query->getRootAliases());
            
            if ($user->isDistributor()) {
                $query->andWhere($query->expr()->gte("$alias.quantity", 0));
            }
            
            // ...
        } 
    ])
```

**Hint**: if same definition of `CallbackFilter` is used multiple times, consider creating custom filter class, to reduce repeated code (see [creating custom Doctrine ORM filter](../../types-reference.md#creating-custom-filter-class--doctrine-orm-)).

## Inherited options

See [abstract column type documentation](../../other/abstract.md).

## Overridden options

### `field_type`

**type**: `string` **default**: `'Symfony\Bridge\Doctrine\Form\Type\EntityType'`
