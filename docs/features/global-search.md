---
order: f
---

# Global search

![Search filter input with the Tabler theme](./../static/global_search.png)--

While the filters may be useful in many cases, they are tied to a specific fields.
Sometimes all the user needs is a single text input, to quickly search through multiple fields.

To handle that, there's a built-in special filter, which allows doing exactly that.
The uniqueness of this filter shines in the way it is rendered - in the built-in themes, instead of showing up in the filter form, it gets displayed above, always visible, easily accessible.

## Adding the search handler

To define a search handler, use the builder's `setSearchHandler()` method to provide a callable, 
which gets an instance of query, and a search string as its arguments:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQuery;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->setSearchHandler($this->handleSearchFilter(...))
        ;
    }
    
    private function handleSearchFilter(DoctrineOrmProxyQuery $query, string $search): void
    {
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
}
```

!!!
**Tip**: Move the search handler logic into repository to reduce the type class complexity.
!!!

## Adding the search filter

The global search requires the user to provide a search query. 
This is handled by the [SearchFilterType](../reference/filters/types/search.md), which simply renders the search input.
To help with that process, if the search handler is defined, the search filter will be added automatically.

This filter will be named `__search`, which can be referenced using the constant:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;

$filter = $builder->getFilter(DataTableBuilderInterface::SEARCH_FILTER_NAME);
```

This behavior can be disabled (or enabled back again) using the builder's method:

```php
$builder->setAutoAddingSearchFilter(false);
```

!!!
**Tip**: Because the global search is treated as a regular filter, it supports the [filtering persistence](filtering.md#configuring-the-feature-persistence).
!!!
