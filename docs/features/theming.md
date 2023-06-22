---
order: h
---

# Theming

Every HTML part of this bundle can be customized using [Twig](https://twig.symfony.com/) themes.

## Built-in themes

The following themes are natively available in the bundle:

- [:icon-mark-github: @KreyuDataTable/themes/bootstrap_5.html.twig](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/bootstrap_5.html.twig) - integrates [Bootstrap 5](https://getbootstrap.com/docs/5.0/);
- [:icon-mark-github: @KreyuDataTable/themes/tabler.html.twig](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/tabler.html.twig) - integrates [Tabler UI Kit](https://tabler.io/);
- [:icon-mark-github: @KreyuDataTable/themes/base.html.twig](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/base.html.twig) - base HTML template;

By default, the [:icon-mark-github: @KreyuDataTable/themes/base.html.twig](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/base.html.twig) theme is used.

!!! Note
The default template provides minimal HTML required to properly display the data table.
!!!

## Selecting a theme

To select a theme, use `themes` option.

For example, in order to use the [Bootstrap 5](https://getbootstrap.com/docs/5.0/) theme:

+++ Globally (YAML)
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    themes:
      - '@KreyuDataTable/themes/bootstrap_5.html.twig'
```
+++ Globally (PHP)
```php # config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->defaults()->themes([
        '@KreyuDataTable/themes/bootstrap_5.html.twig',
    ]);
};
```
+++ For data table type
```php # src/DataTable/Type/ProductDataTable.php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'themes' => [
                '@KreyuDataTable/themes/bootstrap_5.html.twig',
            ],
        ]);
    }
}
```
+++ For specific data table
```php # src/Controller/ProductController.php
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
                    '@KreyuDataTable/themes/bootstrap_5.html.twig',
                ],
            ],
        );
    }
}
```
+++

## Customizing existing theme

To customize existing theme, you can either:

- create a template that extends one of the built-in themes;
- create a template that [overrides the built-in theme](https://symfony.com/doc/current/bundles/override.html#templates);
- create a template from scratch;

Because `themes` configuration option accepts an array of themes,
you can provide your own theme with only a fraction of Twig blocks,
using the built-in themes as a fallback, for example:

```twig
{# templates/data_table/theme.html.twig #}
{% block column_boolean_value %}
    {# ... #}
{% endblock %}
```

+++ Globally (YAML)
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    themes:
      - '@KreyuDataTable/themes/bootstrap_5.html.twig'
```
+++ Globally (PHP)
```php # config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->defaults()->themes([
        'templates/data_table/theme.html.twig',
        '@KreyuDataTable/themes/bootstrap_5.html.twig',
    ]);
};
```
+++ For data table type
```php # src/DataTable/Type/ProductDataTable.php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'themes' => [
                'templates/data_table/theme.html.twig',
                '@KreyuDataTable/themes/bootstrap_5.html.twig',
            ],
        ]);
    }
}
```
+++ For specific data table
```php # src/Controller/ProductController.php
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
                    'templates/data_table/theme.html.twig',
                    '@KreyuDataTable/themes/bootstrap_5.html.twig',
                ],
            ],
        );
    }
}
```
+++
