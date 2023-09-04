---
order: f
---

# Enabling global search


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

Now that the data table has full filtration and search capabilities, let's focus on something that may be really important in some use cases — [exporting the data](exporting-the-data.md).
