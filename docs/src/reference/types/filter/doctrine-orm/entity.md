<script setup>
    import FilterTypeOptions from "../options/filter.md";
    import DoctrineOrmFilterTypeOptions from "../options/doctrine-orm.md";
</script>

# Doctrine ORM EntityFilterType

The Doctrine ORM [`EntityFilterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/Type/EntityFilterType.php) represents a filter that operates on an entity values.

## Options

### `choice_label`

This is the property that should be used for displaying the entities as text in the active filter HTML element.

```php
use App\Entity\Category;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\EntityFilterType;
// ...

$builder->addFilter('category', EntityFilterType::class, [
    'form_options' => [
        'class' => Category::class,
        'choice_label' => 'displayName', // choice label for form
    ],
    'choice_label' => 'displayName', // separate choice label for data table filter
]);
```

If left blank, the entity object will be cast to a string and so must have a `__toString()` method. You can also pass a callback function for more control:

```php
use App\Entity\Category;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\EntityFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
// ...

$builder->addFilter('category', EntityFilterType::class, [
    'form_options' => [
        'class' => Category::class,
        'choice_label' => 'displayName',
    ],
    'choice_label' => function (FilterData $data): string {
        return $data->getValue()->getDisplayName();
    },
]);
```

::: tip This option works like a `choice_label` option in `ChoiceType` form option. 
When passing a string, the `choice_label` option is a property path. So you can use anything supported by the [PropertyAccess component](https://symfony.com/doc/current/components/property_access.html).

For example, if the translations property is actually an associative array of objects, each with a name property, then you could do this:

```php
use App\Entity\Category;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\EntityFilterType;
// ...

$builder->addFilter('category', EntityFilterType::class, [
    'form_options' => [
        'class' => Category::class,
        'choice_label' => 'translations[en].name',
    ],
    'choice_label' => 'translations[en].name',
]);
```
:::

## Inherited options

<FilterTypeOptions :defaults="{
    formType: 'Symfony\\Bridge\\Doctrine\\Form\\Type\\EntityType',
    formOptions: `['choice_value' => 'Identifier name of the entity class if 'class' form option is given, e.g. id']`
}" />

<DoctrineOrmFilterTypeOptions/>