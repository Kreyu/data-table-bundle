# How to Create a Custom Column Type

This bundle comes with multiple column types, ready to use in your applications.
However, it's common to create custom column types to solve specific purposes in your projects.

## Creating Column Types Based on Built-in Types

The easiest way to create a column type is to base it on one of the [existing column types]().
Imagine, that your project displays a column with link to the related entity `show` view.
This can be implemented with a [LinkType](), where the `href` option is set to the url to the `show` view:

```php
// src/DataTable/Type/ProductType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\LinkType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductType extends AbstractType
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            // ...
            ->addColumn('category', LinkType::class, [
                'href' => function (Category $category): string {
                    return $this->urlGenerator->generate('app_category_show', [
                        'id' => $category->getId(),
                    ]);
                },
                'formatter' => function (Category $category): string {
                    return $category->getName();
                },
            ])
        ;
    }
}
```

However, if you use the same column type in several data tables, repeating the generation of `href` and `formatter` options quickly becomes boring.
In this example, a better solutions is to create a custom column type based on `LinkType`. 
The custom type looks and behaves like a `LinkType`, but the `href` and `formatter` options are already populated, so you don't need to define them.

Column types are PHP classes that implement [ColumnTypeInterface](), but you should instead extend from [AbstractType](), 
which already implements that interface and provides some utilities. 
By convention, they are stored in the `src/DataTable/Column/Type/` directory:

```php
// src/DataTable/Column/Type/CategoryLinkType.php
namespace App\DataTable\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\AbstractType;
use Kreyu\Bundle\DataTableBundle\Column\Type\LinkType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoryLinkType extends AbstractType
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'href' => function (Category $category): string {
                return $this->urlGenerator->generate('app_category_show', [
                    'id' => $category->getId(),
                ]);
            },
            'formatter' => function (Category $category): string {
                return $category->getName();
            },
        ]);
    }
    
    public function getParent(): ?string
    {
         return LinkType::class;
    }
}
```

[getParent()]() tells bundle to take `LinkType` as a starting point, then `configureOptions()` overrides some of its options. 
(All methods of the ColumnTypeInterface are explained in detail later in this article.) 
The resulting column type is a link column with predefined `href` and `formatter` options.

Now, you can add this column type when creating data table:

```php
// src/DataTable/Type/ProductType.php
namespace App\DataTable\Type;

use App\DataTable\Column\Type\CategoryLinkType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractType;

class ProductType extends AbstractType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            // ...
            ->addColumn('category', CategoryLinkType::class)
        ;
    }
}
```

## Creating Column Types Created From Scratch

Some column types are so specific to your projects that they cannot be based on any existing form types because they are too different.
Consider an application, that wants to display a tonnage in many units.

As explained above, column types are PHP classes that implement [ColumnTypeInterface](), although it's more convenient to extend instead from [AbstractType]():

```php
// src/DataTable/Column/Type/QuantityType.php
namespace App\DataTable\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\AbstractType;

class QuantityType extends AbstractType
{
    // ...
} 
```

These are the most important methods that a column type class can define:

`buildView()`

It sets any extra variables you'll need when rendering the column in a template.

`configureOptions()`

It defines the options configurable when using the column type, which are also the options that can be used in `buildView()` method. 
Options are inherited from parent types and parent type extensions, but you can create any custom option you need.

`getParent()`

If your custom type is based on another type (i.e. they share some functionality), add this method to return the fully-qualified class name of that original type. 
**Do not use PHP inheritance for this**. This bundle will call all the column type methods and type extensions of the parent before calling the ones defined in your custom type.

Otherwise, if your custom type is build from scratch, you can omit `getParent()`.

By default, the `AbstractType` class returns the generic [ColumnType]() type, which is the root parent for all column types.

## Adding Configuration Options for the Column Type

Imagine that your project requires to make the `QuantityType` configurable in two ways:

- the given quantity should be automatically converted to the requested unit;
- some quantity columns should be displayed in different formats than the others;

This is solved with "column type options", which allow to configure the behavior of the column types.
The options are defined in the `configureOptions()` method, and you can use all the [OptionsResolver component features]() to define, validate and process their values:

```php
// src/DataTable/Column/Type/QuantityType.php
namespace App\DataTable\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuantityType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        // this defines the available options and their default values when
        // they are not configured explicitly when using the column type
        $resolver->setDefaults([
            'decimals' => 0,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
        ]);
        
        // this defines the available options that are required to be configured explicitly
        $resolver->setRequired([
            'unit_from',
            'unit_to',
        ]);
        
        // optionally you can also restrict the options type or types (to get
        // automatic type validation and useful error messages for end users)
        $resolver->setAllowedTypes('decimals', ['int']);
        $resolver->setAllowedTypes('decimal_separator', ['null', 'string']);
        $resolver->setAllowedTypes('thousands_separator', ['null', 'string']);
    }
} 
```

Now you can configure these options when using the column type:

```php
// src/DataTable/Type/ProductType.php
namespace App\DataTable\Type;

use App\DataTable\Column\Type\QuantityType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractType;

class ProductType extends AbstractType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            // ...
            ->addColumn('quantity', QuantityType::class, [
                'unit_from' => 'g',
                'unit_to' => 't',
                // decimals, decimal_separator and thousands_separator options
                // are not configured explicitly, so their default value will be used
            ])
        ;
    }
}
```

## Creating the Column Type Template

By default, custom column types will be rendered using the [data table themes]() configured in the application.
However, for some types you may prefer to create a custom template in order to customize how they look or their HTML structure.

First, create a new Twig template anywhere in the application to store the fragments used to render the types:

```twig
{# templates/data_table/theme.html.twig #}
{% extends '@KreyuDataTable/themes/bootstrap_5.html.twig' %}

{# ... here you will add the Twig code ... #}
```

> ### 💡 Important note
> Notice the use of the `extends` on built-in bundle theme.
> By doing it this way, you can overwrite only specific parts of the theme.

Then, update the [theme configuration option]() to use this new template:

```yaml
# config/packages/kreyu_data_table.yaml
kreyu_data_table:
    theme: 'data_table/theme.html.twig' 
```

The last step is to create the actual Twig template that will render the type.
The template contents depend on which HTML, CSS and JavaScript frameworks and libraries are used in your application:

```twig
{# templates/data_table/theme.html.twig #}
{% extends '@KreyuDataTable/themes/bootstrap_5.html.twig' %}

{% block kreyu_data_table_quantity %}
    {# ... #}
{% endblock %}
```

Every block is prefixed with `kreyu_data_table_` by default.
Last part of the Twig block name (e.g. `quantity`) comes from the class name (`QuantityType` -> `quantity`).
This can be controlled by overriding the `getBlockPrefix()` method in `QuantityType`.

## Passing Variables to the Column Type Template

The bundle passes a series of variables to the template used to render the column type.
You can also pass your own variables, which can be based on the options defined by the column or be completely independent:

```php
// src/DataTable/Column/Type/QuantityType.php
namespace App\DataTable\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Kreyu\Bundle\DataTableBundle\Column\Type\AbstractType;
use Kreyu\Bundle\DataTableBundle\Column\Type\LinkType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class QuantityType extends AbstractType
{
    public function __construct(private UnitConverterInterface $unitConverter)
    {
    }
    
    public function buildView(ColumnView $view, ColumnInterface $column, array $options): void
    {    
        // pass the custom options directly to the template
        $view->vars['decimals'] = $options['decimals'];
        $view->vars['decimal_separator'] = $options['decimal_separator'];
        $view->vars['thousands_separator'] = $options['thousands_separator'];
        
        // create an additional variable named "converted_value" that will hold the value after the conversion
        $view->vars['converted_value'] = $view->vars['value'] ?? null;
        
        if (null !== $view->vars['converted_value']) {
            // use some implementation of unit converter to do the heavy work
            $view->vars['converted_value'] = $this->unitConverter
                ->convert($value)
                ->from($options['unit_from'])
                ->to($options['unit_to'])
            ;
        }
    }
}
```

The variables added in `buildView()` are available in the column type template as any other regular Twig variable:

```twig
{# templates/data_table/theme.html.twig #}
{% extends '@KreyuDataTable/themes/bootstrap_5.html.twig' %}

{% block data_table_quantity %}
    {% if value is not null %}
        {{- value|number_format(decimals, decimal_separator, thousands_separator) -}}
    {% endif %}
{% endblock %}
```