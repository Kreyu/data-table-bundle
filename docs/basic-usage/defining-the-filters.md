---
order: e
---

# Defining the filters

One of the most important features of the data tables is the ability to filter the data.\
Similar to data tables and its columns, the filters are using the [Types API](../philosophy/understanding-the-types-api.md).

## Adding filters to the data table

Let's start by adding a filter for each field in the product entity:

{% code title="src/DataTable/Type/ProductDataTableType.php" lineNumbers="true" %}
```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Columns added before...
        
        $builder
            ->addFilter('id', NumericFilterType::class)
            ->addFilter('name', StringFilterType::class)
            ->addFilter('createdAt', DateRangeFilterType::class)
        ;
    }
}
```
{% endcode %}

First argument represents a filter name, which also represents a property in the Product entity.\
The second argument represents a fully qualified class name of a filter type, which similarly to data table type classes, works as a blueprint for a filter - and describes how to render & handle it.

For reference, see [built-in filter types](../reference/filters/types.md).

## Specifying the filter operator

When trying out the defined filters, one thing may be striking - the `name` filter matches exact string, instead of matching it partially. This, in most cases, may be crucial for the user.

Introducing filter operators - where each filter can support multiple operators, such as "equals", "contains", "starts with", etc. Optionally, the filtration form can display the operator selector, letting the user select a desired filtration method.

For this example, let's work through two scenarios:

1. configuring the `name` filter to partial match the string, instead of exact matching, without showing the operator selector to the user;
2. configuring the `name` filter to show operator selector to the user;

Similar to data table and column types, filter types can be configured using the options array, passed as the third parameter to the `addFilter()` method.&#x20;

#### **Scenario I - specifying default operator**

By default, each filter defines an array of supported operators. Those operators are then available to select by the user in the form. If operator selector is not visible, then the **first choice** is used.&#x20;

In case of the string filter, the default operator is "EQUALS", because it is first in the supported operators array, stored in the `operator_options.choices` option. To change the default operator to "CONTAINS", set the choices option to an array containing it as the first entry:

{% code title="" lineNumbers="true" %}
```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Columns added before...
        
        $builder
            ->addFilter('id', NumericFilterType::class)
            ->addFilter('name', StringFilterType::class, [
                'operator_options' => [
                    'choices' => [
                        Operator::CONTAINS,
                    ],
                ],
            ])
            ->addFilter('createdAt', DateRangeFilterType::class)
        ;
    }
}
```
{% endcode %}

#### Scenario II - displaying operator selector

By default, the operator selector is not visible, because the `operator_options.visible` equals `false`. To change that, set the option to `true`:

{% code title="src/DataTable/Type/ProductDataTableType.php" lineNumbers="true" %}
```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Columns added before...
        
        $builder
            ->addFilter('id', NumericFilterType::class)
            ->addFilter('name', StringFilterType::class, [
                'operator_options' => [
                    'visible' => true,
                ],
            ])
            ->addFilter('createdAt', DateRangeFilterType::class)
        ;
    }
}
```
{% endcode %}

Of course, it is possible to define both options at once, restricting operators visible to the user.

Being on the subject of filters, let's continue by enabling a [global search feature](enabling-global-search.md).
