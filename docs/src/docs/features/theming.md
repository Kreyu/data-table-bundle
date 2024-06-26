# Theming

Every HTML part of this bundle can be customized using [Twig](https://twig.symfony.com/) themes.

[[toc]]

## Built-in themes

The following themes are natively available in the bundle:

- [`@KreyuDataTable/themes/bootstrap_5.html.twig`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/bootstrap_5.html.twig) - integrates [Bootstrap 5](https://getbootstrap.com/docs/5.0/);
- [`@KreyuDataTable/themes/tabler.html.twig`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/tabler.html.twig) - integrates [Tabler UI Kit](https://tabler.io/);
- [`@KreyuDataTable/themes/base.html.twig`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/base.html.twig) - base HTML template;

By default, the [`@KreyuDataTable/themes/base.html.twig`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/base.html.twig) theme is used.

## Selecting a theme

There are many ways to configure a theme for the data table.
In most cases, you will want to use the same theme for all data tables, so it is recommended to configure the theme globally:

::: code-group
```yaml [YAML]
# config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    themes:
      - '@KreyuDataTable/themes/bootstrap_5.html.twig'
```

```php [PHP]
// config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->defaults()->themes([
        '@KreyuDataTable/themes/bootstrap_5.html.twig',
    ]);
};
```
:::

Because the bundle configuration `defaults` key defines _default_ options for the data tables, you can still overwrite the option for a specific data table type:

```php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('themes', [
            '@KreyuDataTable/themes/bootstrap_5.html.twig',
        ]);
    }
}
```

Because the data table type defines _default_ options for the data table _type_, you can still overwrite the option for a specific data table:

```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    public function index(Request $request)
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $query,
            options: [
                'themes' => [
                    '@KreyuDataTable/themes/bootstrap_5.html.twig',
                ],
            ]
        );
    }
}
```

## Applying themes in Twig

Similar to forms, you can set the data table themes directly in the Twig template, by using the `data_table_theme` tag:

```twig
{% data_table_theme products 'themes/data_table.html.twig' %}

{{ data_table(products) }}
```

If you wish to use multiple themes, pass an array using the `with` keyword:

```twig
{% data_table_theme products with [
    'themes/data_table.html.twig', 
    '@KreyuDataTable/themes/bootstrap_5.html.twig',
] %}

{{ data_table(products) }}
```

If you wish to disable currently configured themes for the data table and **only** use given ones, add the `only` keyword after the list of data table themes:

```twig
{% data_table_theme products with ['themes/data_table.html.twig'] only %}

{{ data_table(products) }}
```

## Customizing existing theme

To customize existing theme, you can either:

- create a template that extends one of the built-in themes;
- create a template that [overrides the built-in theme](https://symfony.com/doc/current/bundles/override.html#templates);
- create a template from scratch;

Because `themes` configuration option accepts an array of themes,
you can provide your own theme with only a fraction of Twig blocks,
using the built-in themes as a fallback, for example:

```twig
{# themes/data_table.html.twig #}
{% block column_boolean_value %}
    {# ... #}
{% endblock %}
```

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    themes:
      - 'themes/data_table.html.twig',
      - '@KreyuDataTable/themes/bootstrap_5.html.twig'
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->defaults()->themes([
        'themes/data_table.html.twig',
        '@KreyuDataTable/themes/bootstrap_5.html.twig',
    ]);
};
```

```php [For data table type]
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'themes' => [
                'themes/data_table.html.twig',
                '@KreyuDataTable/themes/bootstrap_5.html.twig',
            ],
        ]);
    }
}
```

```php [For specific data table (PHP)]
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index()
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $query,
            options: [
                'themes' => [
                    'themes/data_table.html.twig',
                    '@KreyuDataTable/themes/bootstrap_5.html.twig',
                ],
            ],
        );
    }
}
```

```php [For specific data table (Twig)]
<div class="card">
    {{ data_table(products, { 
        themes: [
            'themes/data_table.html.twig',
            '@KreyuDataTable/themes/bootstrap_5.html.twig',
        ]
    }) }}
</div>
```
:::