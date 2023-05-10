---
order: f
---

# Enabling global search

While the filters may be useful in many cases, they are tied to a specific fields.\
Sometimes all the user need is a single text input, to quickly search through multiple fields.

To handle that, there's a built-in special filter, which allows doing exactly that. \
The uniqueness of this filter shines in the way it is rendered - in the built-in themes, instead of showing up in the filter form, it gets displayed above, always visible, easily accessible.

<figure><img src="../.gitbook/assets/image (1) (1).png" alt=""><figcaption><p>Search filter input with the built-in Tabler theme</p></figcaption></figure>

## Adding the special search filter

To start, define a filter of search type - its name doesn't really matter:

{% code title="src/DataTable/Type/ProductDataTableType.php" lineNumbers="true" %}
```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Columns and filters added before...
        
        $builder
            ->addFilter('search', SearchFilterType::class)
        ;
    }
}
```
{% endcode %}

Trying to display the data table with this filter configuration will result in an error:

```markup
*MissingOptionsException*: The required option "handler" is missing.
```

This is because the search filter type requires a `handler` option, which contains all the logic required for the data table search capabilities. The option accepts a callable, which gets an instance of query, and a search string as its arguments:

{% code title="src/DataTable/Type/ProductDataTableType.php" lineNumbers="true" %}
```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Columns and filters added before...
        
        $builder
            ->addFilter('search', SearchFilterType::class, [
                'handler' => $this->handleSearchFilter(...),
            ])
        ;
    }
    
    /**
     * @param DoctrineOrmProxyQuery $query
     */
    private function handleSearchFilter(ProxyQueryInterface $query, string $search): void
    {
        $criteria = $query->expr()->orX(
            $query->expr()->like('product.id', ':search'),
            $query->expr()->like('product.name', ':search'),
        );
        
        $query
            ->andWhere($criteria)
            ->setParameter('search', '%' . $search . '%')
        ;
    }
}
```
{% endcode %}

{% hint style="info" %}
**Tip**

Move the search handler logic into repository to reduce the complexity.
{% endhint %}

Now that the data table has full filtration and search capabilities, let's focus on something that may be really important in some use cases - [exporting the data](exporting-the-data.md).
