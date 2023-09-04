---
label: Search
---

# Search filter type

The `SearchFilterType` represents a special filter to handle the [global search](../../../features/global-search.md) feature.

+---------------------+--------------------------------------------------------------+
| Parent type         | [FilterType](../filter)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: SearchFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/SearchFilterType.php)
+---------------------+--------------------------------------------------------------+
| Form Type           | [SearchType](https://symfony.com/doc/current/reference/forms/types/search.html)
+---------------------+--------------------------------------------------------------+
| Supported operators | Supports all operators
+---------------------+--------------------------------------------------------------+

## Options

### `handler`

**type**: `callable`

Sets callable that operates on the query passed as a first argument:

```php #
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQuery;

$builder
    ->addFilter('search', SearchFilterType::class, [
        'handler' => function (DoctrineOrmProxyQuery $query, string $search): void {
            $alias = current($query->getRootAliases());

            // Remember to use parameters to prevent SQL Injection!
            // To help with that, DoctrineOrmProxyQuery has a special method "getUniqueParameterId",
            // that will generate a unique parameter name (inside its query context), handy!
            $parameter = $query->getUniqueParameterId(); 
            
            $query
                ->andWhere($query->expr()->eq("$alias.type", ":$parameter"))
                ->setParameter($parameter, $data->getValue())
            ;
            
            $criteria = $query->expr()->orX(
                $query->expr()->like("$alias.id", ":$parameter"),
                $query->expr()->like("$alias.name", ":$parameter"),
            );
            
            $query
                ->andWhere($criteria)
                ->setParameter($parameter, "%$search%")
            ;
        } 
    ])
```

## Inherited options

{{ option_label_default_value = '`false`' }}
{{ option_form_type_default_value = '`\'Symfony\\Component\\Form\\Extension\\Core\\Type\\SearchType\'`' }}

{% capture option_form_options_notes %}
The normalizer ensures the default `['attr' => ['placeholder' => 'Search...']]` is added.
{% endcapture %}

{{ include '_filter_options' }}
