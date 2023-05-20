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

To select a theme, provide which one to use in the bundle configuration file.
For example, in order to use the [Bootstrap 5](https://getbootstrap.com/docs/5.0/) theme:

+++ YAML
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  themes:
    - '@KreyuDataTable/themes/bootstrap_5.html.twig'
```
+++ PHP
```php # config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->themes([
        '@KreyuDataTable/themes/bootstrap_5.html.twig',
    ]);
};
```
+++

For more information, see ["themes" option configuration reference](../reference/configuration.md#themes).

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
{% block kreyu_data_table_column_boolean %}
    {# ... #}
{% endblock %}
```

+++ YAML
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  themes:
    - 'templates/data_table/theme.html.twig'
    - '@KreyuDataTable/themes/bootstrap_5.html.twig'
```
+++ PHP
```php # config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->themes([
        'templates/data_table/theme.html.twig',
        '@KreyuDataTable/themes/bootstrap_5.html.twig',
    ]);
};
```
+++

For more information, see ["themes" option configuration reference](../reference/configuration.md#themes).
