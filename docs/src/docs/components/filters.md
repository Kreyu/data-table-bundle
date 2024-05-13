# Filters

[[toc]]

## Adding filters

To add a filter, use the data table builder's `addFilter()` method:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateTimeFilterType;

class UserDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('id', NumericFilterType::class)
            ->addFilter('name', StringFilterType::class)
            ->addFilter('createdAt', DateTimeFilterType::class)
        ;
    }
}
```

This method accepts _three_ arguments:

- filter name;
- filter type — with a fully qualified class name;
- filter options — defined by the filter type, used to configure the filter;

For reference, see [available filter types](../../reference/types/filter.md).

## Creating filter types

This bundle comes with plenty of the [built-in filter types](../../reference/types/filter.md). 
However, those may not cover complex cases. Luckily, creating custom filter types are easy.

Filter types are classes that implement [`FilterTypeInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/FilterTypeInterface.php). However, it's better to extend from the [`AbstractFilterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/AbstractFilterType.php):

```php
use Kreyu\Bundle\DataTableBundle\Filter\Type\AbstractFilterType;

class PhoneNumberFilterType extends AbstractFilterType
{
}
```

<div class="tip custom-block" style="padding-top: 8px;">

Recommended namespace for the filter type classes is `App\DataTable\Filter\Type\`.

</div>

### Filter type inheritance

If you take a look at the [`AbstractFilterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/AbstractFilterType.php), you'll see that `getParent()` method returns fully-qualified name of the `FilterType` type class.
This is the type that defines all the required options, such as `label`, `form_type`, `form_options`, etc.

::: danger This is not recommended: do _not_ use PHP inheritance!
```php
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;

class PhoneNumberFilterType extends StringFilterType
{
}
```
:::

::: tip This is recommended: provide parent using the `getParent()` method
```php
use Kreyu\Bundle\DataTableBundle\Filter\Type\AbstractFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;

class PhoneNumberFilterType extends AbstractFilterType
{
    public function getParent(): ?string
    {
        return StringFilterType::class;
    }
}
```
:::

Both methods _will work_, but using PHP inheritance may result in unexpected behavior when using the [filter type extensions](#filter-type-extensions).

### Form type and options

To define form type and its options for the filter, use `form_type` and `form_options` options:

```php
use Kreyu\Bundle\DataTableBundle\Filter\Type\AbstractFilterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColorFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'form_type' => ChoiceType::class,
            'form_options' => [
                'choices' => [
                    '#F44336' => 'Red',
                    '#4CAF50' => 'Green',
                    '#2196F3' => 'Blue',
                ],
            ],
        ]);
    }
}
```

### Creating filter handler

Filter type classes is used to define the filter, not the actual logic executed when the filter is used.
This logic should be delegated to a filter handler instead. Filter handlers are classes that implement [`FilterHandlerInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/FilterHandlerInterface.php):

```php
use Kreyu\Bundle\DataTableBundle\Filter\FilterHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;

class CustomFilterHandler implements FilterHandlerInterface
{
    public function handle(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void
    {
        // ...
    }
}
```
<div class="tip custom-block" style="padding-top: 8px;">

For example, take a look at the [`DoctrineOrmFilterHandler`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/DoctrineOrmFilterHandler.php),
which is used by all Doctrine ORM integration filter types.

</div>

The filter handler can be applied to a custom filter type by using the filter builder's `setHandler()` method:

```php
use Kreyu\Bundle\DataTableBundle\Filter\Type\AbstractFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;

class CustomFilterType extends AbstractFilterType
{
    public function buildFilter(FilterBuilderInterface $builder, array $options): void
    {
        $builder->setHandler(new CustomFilterHandler());
    }
}
```

If separate class seems like an overkill, you can implement the handler interface on the type class instead:

```php
use Kreyu\Bundle\DataTableBundle\Filter\Type\AbstractFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;

class CustomFilterType extends AbstractFilterType implements FilterHandlerInterface
{
    public function buildFilter(FilterBuilderInterface $builder, array $options): void
    {
        $builder->setHandler($this);
    }
    
    public function handle(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void
    {
        // ...
    }
}
```

## Filter type extensions

Filter type extensions allows modifying configuration of the existing filter types, even the built-in ones.
Let's assume, that we want to [change default operator](#changing-default-operator) of [`StringFilterType`](#)
to `Operator::Equals`, so it is not necessary to pass `default_operator` option for each filter using this type.

Filter type extensions are classes that implement [`FilterTypeExtensionInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Extension/FilterTypeExtensionInterface.php).
However, it's better to extend from the [`AbstractFilterTypeExtension`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Extension/AbstractColumnTypeExtension.php):

```php
use Kreyu\Bundle\DataTableBundle\Filter\Extension\AbstractFilterTypeExtension;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultOperatorStringFilterTypeExtension extends AbstractFilterTypeExtension
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('default_operator', Operator::Equals);
    }
    
    public static function getExtendedTypes(): iterable
    {
        return [StringFilterType::class];
    }
}
```

If your extension aims to cover every filter type in the system, provide the base [`FilterType`](#) in the `getExtendedTypes()` method.

## Formatting active filter value

When the filter is active, its value is rendered to the user as a "pill", which removes the filter upon clicking it.
By default, the filter value requires to be stringable. However, there are some cases, where value cannot be stringable.

Let's assume, that the application contains a `Product` entity, which contains a `Category`, which is **not** stringable:

```php
readonly class Product
{
    public function __construct(
        public Category $category,
    )
}

readonly class Category
{
    public function __construct(
        public string $name,
    )
}
```

In the product data table, we want to filter products by their category. 
Using [EntityFilterType](#) will allow selecting a category from a list of existing categories.
Unfortunately, when the filter is applied, a `Cannot convert value of type Category to string` exception will occur.

In that case, you can use the `active_filter_formatter` option, to determine what should be rendered based on the filter data:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\EntityFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('category', EntityFilterType::class, [
                'form_options' => [
                    'class' => Category::class,
                    'choice_label' => 'name',
                ],
                'active_filter_formatter' => function (FilterData $data) {
                    $value = $data->getValue();
                    
                    if ($value instanceof Category) {
                        return $value->getName();
                    }
                    
                    return $value;
                },
            ])
        ;
    }
}
```

::: tip This is only a simple example of using the `active_filter_formatter` option.
The [`EntityFilterType`](#) has a `choice_label` option, which can be used to provide property path to the value to render:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\EntityFilterType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('category', EntityFilterType::class, [
                'form_options' => [
                    'class' => Category::class,
                    'choice_label' => 'name',
                ],
                'choice_label' => 'name', // same as the form choice_label option
            ])
        ;
    }
}
```
:::

## Changing default operator

Let's assume, that the application contains a `Book` entity with ISBN:

```php
readonly class Book
{
    public function __construct(
        public string $isbn,
    )
}
```

If we use a [StringFilterType](#) on the `isbn` column, the filter will perform partial matching (`LIKE %value%`),
because the filter type has `default_operator` option set to `Operator::Contains`. 
In this case, we want to perform exact matching, therefore, we have to change this option value to `Operator::Equals`:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('isbn', StringFilterType::class, [
                'default_operator' => Operator::Equals,
            ])
        ;
    }
}
```

<div class="tip custom-block" style="padding-top: 8px;">

Each filter supports different set of operators.

</div>

<div class="tip custom-block" style="padding-top: 8px;">
 
To change default operator filter type without having to explicitly provide the `default_operator`, 
consider creating a [filter type extension](#filter-type-extensions).

</div>

## Displaying operator selector

The operator can be selected by the user, when operator selector is visible.
By default, operator selector is **not** visible. To change that, use `operator_visible` option:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('isbn', StringFilterType::class, [
                'operator_visible' => true,
            ])
        ;
    }
}
```

## Operator form type and options

You can customize form type and options of the operator form field, using `operator_form_type` and `operator_form_options`:

```php
use Kreyu\Bundle\DataTableBundle\Filter\Type\AbstractFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Note: this is default operator type
            'operator_form_type' => OperatorType::class,
            'operator_form_options' => [
                'required' => true,
            ],
        ]);
    }
}
```
