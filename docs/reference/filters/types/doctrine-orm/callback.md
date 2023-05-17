---
label: Callback
visibility: hidden
order: g
---

# Callback filter type

The `CallbackFilterType` represents a filter that operates on identifier values.

Displayed as a selector, allows the user to select a specific entity loaded from the database, to query by its identifier.

+---------------------+--------------------------------------------------------------+
| Parent type         | [FilterType](../../filter)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: CallbackFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/CallbackFilterType.php)
+---------------------+--------------------------------------------------------------+
| Form Type           | [TextType](https://symfony.com/doc/current/reference/forms/types/text.html)
+---------------------+--------------------------------------------------------------+
| Supported operators | Supports all operators, but it doesn't affect the actual query.
+---------------------+--------------------------------------------------------------+

## Options

### `callback`

**type**: `callable`

Sets callable that operates on the query passed as a first argument:

```php #
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\CallbackFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;

$builder
    ->addFilter('type', CallbackFilterType::class, [
        'callback' => function (DoctrineOrmProxyQuery $query, FilterData $data, FilterInterface $filter): void {
            $alias = current($query->getRootAliases());

            // Remember to use parameters to prevent SQL Injection!
            // To help with that, DoctrineOrmProxyQuery has a special method "getUniqueParameterId",
            // that will generate a unique parameter name (inside its query context), handy!
            $parameter = $query->getUniqueParameterId(); 
            
            $query
                ->andWhere($query->expr()->eq("$alias.type"), ":$parameter")
                ->setParameter($parameter, $data->getValue())
            ;
        } 
    ])
```

## Inherited options

{{ include '_filter_options' }}
