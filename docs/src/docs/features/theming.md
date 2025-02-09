# Theming

Every HTML part of this bundle can be customized using [Twig](https://twig.symfony.com/) themes.

[[toc]]

## Themes

The following themes are natively available in the bundle:

- [`@KreyuDataTable/themes/bootstrap_5.html.twig`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/bootstrap_5.html.twig) - integrates [Bootstrap 5](https://getbootstrap.com/docs/5.0/)
- [`@KreyuDataTable/themes/tabler.html.twig`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/tabler.html.twig) - integrates [Tabler UI Kit](https://tabler.io/)
- [`@KreyuDataTable/themes/base.html.twig`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/base.html.twig) - base HTML template

By default, the [`@KreyuDataTable/themes/base.html.twig`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/base.html.twig) theme is used.

## Icon themes

The following icon themes are natively available in the bundle:

- [Bootstrap Icons](https://icons.getbootstrap.com/)
  - [`@KreyuDataTable/themes/bootstrap_icons_webfont.html.twig`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/bootstrap_icons_webfont.html.twig)
  - [`@KreyuDataTable/themes/bootstrap_icons_ux.html.twig`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/bootstrap_icons_ux.html.twig)
- [Tabler Icons](https://tabler.io/icons)
  - [`@KreyuDataTable/themes/tabler_icons_webfont.html.twig`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/tabler_icons_webfont.html.twig)
  - [`@KreyuDataTable/themes/tabler_icons_ux.html.twig`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/tabler_icons_ux.html.twig)
- Generic
  - [`@KreyuDataTable/themes/icons_webfont.html.twig`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/tabler_icons_webfont.html.twig)
  - [`@KreyuDataTable/themes/icons_ux.html.twig`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/tabler_icons_ux.html.twig)

Icon themes are separate templates to allow using different icon sets.

Webfont icon themes render icons as `<i>` elements with CSS classes. 
For example, Bootstrap's `check` icon will be rendered as `<i class="bi bi-check"></i>`

The UX icon themes refer to the [Symfony UX Icons](https://symfony.com/bundles/ux-icons), which requires
the `symfony/ux-icons` package. Each icon has to be imported to the project separately, for example:

```shell
symfony console ux:icon:import bi:check
```

When using the generic `icons_webfont.html.twig`, you are expected to provide full icon class names manually,
for example `bi bi-check` instead of simple `check`.

Using the `icons_ux.html.twig` enables you to use the locally stored icons, for example:

- `user-profile` will render icon from `assets/icons/user-profile.svg`
- `admin:user-profile` will render icon from `assets/icons/admin/user-profile.svg`

For more details about how UX icons are loaded, [see Symfony UX Icons documentation](https://symfony.com/bundles/ux-icons/current/index.html#loading-icons).

## Selecting a theme

You can use multiple themes because sometimes data table themes only redefine a few elements.
This way, if some theme doesn't override some element, this bundle looks up in the other themes.

> [!WARNING] The order of the themes is very important!
> Each theme overrides all the previous themes, so you must put the most important themes at the end of the list.

There are many ways to configure a theme for the data table.

In most cases, you will want to use the same theme for all data tables across your application,
so it is recommended to configure the theme globally.

::: code-group
```yaml [YAML]
# config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    themes:
      - '@KreyuDataTable/themes/bootstrap_5.html.twig'
      - '@KreyuDataTable/themes/bootstrap_icons_ux.html.twig'
      - 'themes/data_table.html.twig'
```

```php [PHP]
// config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->defaults()->themes([
        '@KreyuDataTable/themes/bootstrap_5.html.twig',
        '@KreyuDataTable/themes/bootstrap_icons_ux.html.twig',
        'themes/data_table.html.twig',
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
            '@KreyuDataTable/themes/bootstrap_icons_ux.html.twig',
            'themes/data_table.html.twig',
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
                    '@KreyuDataTable/themes/bootstrap_icons_ux.html.twig',
                    'themes/data_table.html.twig',
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
    '@KreyuDataTable/themes/bootstrap_5.html.twig',
    '@KreyuDataTable/themes/bootstrap_icons_ux.html.twig',
    'themes/data_table.html.twig',
] %}

{{ data_table(products) }}
```

If you wish to disable currently configured themes for the data table and **only** use given ones,
add the `only` keyword after the list of data table themes:

```twig
{% data_table_theme products with ['themes/data_table.html.twig'] only %}

{{ data_table(products) }}
```

### One-off theme tweaks

In some cases, you may want to tweak something in the same template you are rendering the data table in.
Assuming that the change is only applied on this specific page, adding a new theme may seem like an overkill.
Instead, you can use the special Twig variable named `_self` to refer to the current template:

```twig
{% data_table_theme products _self %}

{% block content %}
    {{ data_table(products) }}
{% endblock %}

{% block column_number_value %}
    <div class="text-start">
        {{ parent() }}
    </div> 
{% endblock  %}
```

## Adding your own themes

When creating your own theme, you can either create a template that extends one of the built-in themes:

```twig
{# themes/data_table.html.twig #}
{% extends '@KreyuDataTable/themes/bootstrap_5.html.twig' %}

{% block some_theme_block %}
    {# ... #}
{% endblock %}
```

...or create a template from scratch:

```twig
{% block some_theme_block %}
    {# ... #}
{% endblock %}
```

Remember that in the second case, you cannot call the `parent()` function in the block.

### Rendering HTML attributes

The `@KreyuDataTable/themes/base.html.twig` theme provides `attributes` macro can be used to render HTML attributes from an array:

```twig
{% from '@KreyuDataTable/themes/base.html.twig' import attributes %}

<span{{ attributes({ id: 'foo', class: 'text-center' }) }}></span>

{# Rendered as: #}
<span id="foo" class="text-center"></span>
```

If you pass an empty array to the `attributes` macro, it will render an empty string:

```twig
<span{{ attributes([]) }}></span>

{# Rendered as: #}
<span></span>
```

The `attributes` macro will start with a whitespace if at least one attribute is passed.
Because of that, you shouldn't add the whitespace manually:

```twig
{% from '@KreyuDataTable/themes/base.html.twig' import attributes %}

<span {{ attributes({ id: 'foo', class: 'text-center' }) }}></span>

{# Rendered as: #}
<span  id="foo" class="text-center"></span>
```

Notice the double whitespace before the `id` attribute.

In some cases you may want to merge the class attribute with some defaults. To do so, use the Twig `merge` filter:

```twig
{% set attr = { class: 'btn-sm' } #}

{% set attr = attr|merge({ class: 'btn btn-primary ' ~ attr.class }) %}

{# Now "attr" equals to: { class: 'btn btn-primary btn-sm' } #}
```

You can also append the class, instead of prepending:

```twig
{% set attr = { class: 'btn' } #}

{% set attr = attr|merge({ class: (attr.class|default(''))|trim ~ ' btn-primary' }) %}

{# Now "attr" equals to: { class: 'btn btn-primary' } #}
```

If you have no idea whether the `attr` variable contains any `class`, you can use the Twig `default` and `trim` filters to prevent unnecessary whitespace:

```twig
{% set attr = {} #}

{% set attr = attr|merge({ class: ('btn btn-primary ' ~ attr.class|default(''))|trim }) %}

{# Now "attr" equals to: { class: 'btn btn-primary' } #}
```

### Rendering blocks with hierarchy

When creating custom themes, you may find the `data_table_theme_block` Twig function useful.
For example, let's assume the data table has two themes:

```twig
{# themes/theme-a.html.twig #}

{% block column_header %}
    {{ block('column_label') }}
{% endblock %}

{% block column_label %}
    Label A
{% endblock %}
```

```twig
{# themes/theme-b.html.twig #}

{% block column_label %}
    Label B
{% endblock %}
```

In this case, the `column_header` will render "Label A", because it has no idea about theme B.
However, if you use the `data_table_theme_block` instead of the `block`:

```twig
{# themes/theme-a.html.twig #}

{% block column_header %}
    {{ data_table_theme_block(data_table, 'column_label') }}
{% endblock %}

{% block column_label %}
    Label A
{% endblock %}
```

In this case, the `column_header` will render "Label B". The `data_table_theme_block` function will iterate 
through the data table themes render the last block that matches the name, as each theme overrides the previous ones.

In some cases, the `attr` variable may be incorrectly rendered on each nested element. For example:

```twig
{% block column_header %}
    {% set attr = { class: 'text-center' } %}
 
    <span{{ _self.attributes(attr) }}>
        {{ data_table_theme_block(data_table, 'column_label') }}
    </span>
{% endblock %}

{% block column_label %}
    {# When rendered from "column_header", the variable "attr" equals { class: 'text-center' } #}
    <span{{ _self.attributes(attr) }}>
        Label
    </span>
{% endblock %}
```

Rendering the `column_header` block will result in the following HTML:

```
<span class="text-center">
    <span class="text-center">
        Label
    </span>
</span>
```

This is because the `data_table_theme_block` passes the context, so `attr` will be rendered twice.
In some cases this is fine, but sometimes you may want to prevent this behavior. 
To do so, you can pass the `resetAttr` argument set to `true` to the `data_table_theme_block` function.
This will reset the `attr` to an empty array:

```twig
{% block column_header %}
    {% set attr = { class: 'text-center' } %}
 
    <span{{ _self.attributes(attr) }}>
        {{ data_table_theme_block(data_table, 'column_label', resetAttr = true) }}
    </span>
{% endblock %}

{% block column_label %}
    {# When rendered from "column_header", the variable "attr" now equals [] #}
    <span{{ _self.attributes(attr) }}>
        Label
    </span>
{% endblock %}
```

Now, rendering the `column_header` block will result in the following HTML:

```twig
<span class="text-center">
    <span>
        Label
    </span>
</span>
```
