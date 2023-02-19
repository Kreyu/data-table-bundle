# Filtration

A data table can be filtered with a set of _filters_, each of which are built 
with the help of a filter _type_ (e.g. `StringType`, `EntityType`, etc),

## Configuring the filtration feature

By default, the filtration is enabled for every data table type.

Every part of the feature can be configured using the [data table options](#passing-options-to-data-tables):

- `filtration_enabled` - to enable/disable feature completely;
- `filtration_persistence_enabled` - to enable/disable feature [persistence](#persistence);
- `filtration_persistence_adapter` - to change the [persistence adapter](#persistence-adapters);
- `filtration_persistence_subject` - to change the [persistence subject](#persistence-subjects) directly;

By default, if the feature is enabled, the [persistence adapter](#persistence-adapters) 
and [subject provider](#persistence-subject-providers) are autoconfigured.

## Built-in filter types

The following filter types are natively available in the bundle:

- Doctrine ORM
    - [StringType](#stringtype)
    - [NumericType](#numerictype)
    - [EntityType](#entitytype)
    - [CallbackType](#callbacktype)
- Base types
    - [FilterType](#filtertype)

{% include-markdown "filters/creating_custom_filter_type.md" heading-offset=1 %}
{% include-markdown "filters/creating_filter_type_extension.md" heading-offset=1 %}

## Using filter operators

Let's assume, that the product data table contains two products, named:

- Product A
- Product B

There are multiple ways of handling that filtration, for example:

- matching exact string, e.g. _"Product"_ will not find any matches,
- matching only beginning of a string, e.g. _"Product"_ will match both _"Product A"_ and _"Product B"_,

To support such cases, each filter can support a set of operators.

### Displaying filter operator selector to the user

By default, the operator selector is not visible to the user. Because of that, first operator choice is always used.

To display the operator selector, pass the `operator_options.visible` option to the filter:

```php
// src/DataTable/Type/ProductType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractType;

class ProductType extends AbstractType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // ...

        $builder
            ->addFilter('name', StringType::class, [
                'query_path' => 'product.name',
                'operator_options' => [
                    'visible' => true,
                ],
            ])
        ;
    }
}
```

If you wish to restrain operators available to select, pass the `operator_options.choices` option to the filter:

```php
// src/DataTable/Type/ProductType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractType;

class ProductType extends AbstractType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // ...

        $builder
            ->addFilter('name', StringType::class, [
                'query_path' => 'product.name',
                'operator_options' => [
                    'visible' => true,
                    'choices' => [
                        Operator::EQUALS,
                        Operator::STARTS_WITH,
                    ],
                ],
            ])
        ;
    }
}
```

## Changing operator form field type

If you wish to override the operator selector completely, create custom form type 
and pass it as the `operator_type` option. Options passed as `operator_options` are used in that type.

## Built-in types reference

{% include-markdown "filters/types/doctrine-orm/string.md" heading-offset=2 %}
{% include-markdown "filters/types/doctrine-orm/numeric.md" heading-offset=2 %}
{% include-markdown "filters/types/doctrine-orm/entity.md" heading-offset=2 %}
{% include-markdown "filters/types/doctrine-orm/callback.md" heading-offset=2 %}
{% include-markdown "filters/types/filter.md" heading-offset=2 %}
