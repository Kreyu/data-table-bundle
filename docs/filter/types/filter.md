# FilterType

The [FilterType](#) represents a base filter, used as a parent for every other type in the bundle.

## Options

### `label`

**type**: `string` or `TranslatableMessage` **default**: the label is "guessed" from the filter name

Sets the label that will be used when rendering the filter label.

### `label_translation_parameters`

**type**: `array` **default**: `[]`

Sets the parameters used when translating the `label` option.

### `translation_domain`

**type**: `false` or `string` **default**: the default `KreyuDataTable` is used

Sets the translation domain used when translating the filter translatable values.  
Setting the option to `false` disables translation for the filter.

### `query_path`

**type**: `string` **default**: the query path name is "guessed" from the filter name

Sets the field name used in the DQL. For example, if you have a query aliased as `product`, and you want to filter by `name`, then `query_path` should equal `product.name`.

### `field_type`

**type**: `string` **default**: `'Symfony\Component\Form\Extension\Core\Type\TextType'`

Sets the form field type used to render the filter control. 

### `field_options`

**type**: `array` **default**: `[]`

This is the array that's passed to the operator form type specified in the `field_type` option.
For example, if you used the `EntityType` as your `field_type` option (to let user select a specific entity, default for [EntityFilter](../doctrine/orm/entity.md)),
then you'd want to (at least) pass the `class` option to the underlying type, as it is required:

```php
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\EntityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as EntityFormType; 

$builder
    ->addFilter('author', EntityType::class, [
        'field_type' => EntityFormType::class, // default for entity filter type
        'field_options' => [
            'class' => User::class,
        ],    
    ])
;
```

### `operator_type`

**type**: `string` **default**: `'Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType'`

Sets the form field type used to render the filter operator control.

### `operator_options`

**type**: `array` **default**: `[]`

This is the array that's passed to the operator form type specified in the `operator_type` option.
For example, if you used the [OperatorType](../../../src/Filter/Form/Type/OperatorType.php) as your `operator_type` option (by default), 
then you'd want to pass the `visible` and `choices` options to the underlying type:

```php
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\StringFilter;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;

$builder
    ->addFilter('name', StringFilter::class, [
        'operator_options' => [
            'visible' => true,
            'choices' => [
                Operator::EQUAL,
                Operator::CONTAINS,
            ],
        ],    
    ])
;
```
