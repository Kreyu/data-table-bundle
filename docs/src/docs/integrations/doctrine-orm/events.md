# Events

The Doctrine ORM filter handler dispatches two events, which can be used to modify the query parameters and the expression.

## PreSetParametersEvent

The [PreSetParametersEvent](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Event/PreSetParametersEvent.php) is dispatched before `$queryBuilder->setParameter()`
is called for every parameter required by the filter. It can be used to modify the parameters, before they are passed to the query builder.

```php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\DoctrineOrmFilterEvents;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\PreSetParametersEvent;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // ...
        
        $builder
            ->getFilter('name')
            ->addEventListener(DoctrineOrmFilterEvents::PRE_SET_PARAMETERS, function (PreSetParametersEvent $event) {
                $filter = $event->getFilter();
                $data = $event->getData();
                $query = $event->getQuery();
                $parameters = $event->getParameters();

                // ...
                
                $event->setParameters($parameters);
            });
    }
}
```

## PreApplyExpressionEvent

The [PreApplyExpressionEvent](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Event/PreApplyExpressionEvent.php) is dispatched before `$queryBuilder->andWhere()` is called.
It can be used to modify the expression before it is passed to the query builder.

::: tip Use [expression transformers](expression-transformers.md) for easier and reusable solution for modifying the expression. 
Those transformers are called by the [ApplyExpressionTransformers](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/EventListener/ApplyExpressionTransformers.php) event subscriber,
which is automatically used in [DoctrineOrmFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/Type/DoctrineOrmFilterType.php) filter type, as well as
every filter type that uses it as a parent.
:::

```php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\DoctrineOrmFilterEvents;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\PreApplyExpressionEvent;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // ...
        
        $builder
            ->getFilter('name')
            ->addEventListener(DoctrineOrmFilterEvents::PRE_APPLY_EXPRESSION, function (PreApplyExpressionEvent $event) {
                $filter = $event->getFilter();
                $data = $event->getData();
                $query = $event->getQuery();
                $expression = $event->getExpression();
                
                // ...

                $event->setExpression($expression);
            });
    }
}
```