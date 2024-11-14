# Columns

[[toc]]

## Adding columns

To add a column, use the data table builder's `addColumn()` method:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;

class UserDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('id', NumberColumnType::class)
            ->addColumn('name', TextColumnType::class, [
                'label' => 'Full name',
            ])
            ->addColumn('createdAt', DateTimeColumnType::class, [
                'format' => 'Y-m-d H:i:s',            
            ])
        ;
    }
}
```

This method accepts _three_ arguments:

- column name;
- column type — with a fully qualified class name;
- column options — defined by the column type, used to configure the column;

For reference, see [available column types](../../reference/types/column.md).

## Creating column types

If [built-in column types](../../reference/types/column.md) are not enough, you can create your own. 
In following chapters, we'll be creating a column that renders a phone number stored as an object:

```php
readonly class PhoneNumber
{
    public function __construct(
        public string $nationalNumber,
        public string $countryCode,
    )
}
```

Column types are classes that implement [`ColumnTypeInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ColumnTypeInterface.php), although, it's better to extend from the [`AbstractColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/AbstractColumnType.php):

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\AbstractColumnType;

class PhoneNumberColumnType extends AbstractColumnType
{
}
```

<div class="tip custom-block" style="padding-top: 8px;">

Recommended namespace for the column type classes is `App\DataTable\Column\Type\`.

</div>

### Column type inheritance

Because our phone number column fundamentally renders as a text, let's base it off the built-in [`TextColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/TextColumnType.php).
Provide the fully-qualified class name of the parent type in the `getParent()` method:

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\AbstractColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;

class PhoneNumberColumnType extends AbstractColumnType
{
    public function getParent(): ?string
    {
        return TextColumnType::class;
    }
}
```

::: tip
If you take a look at the [`AbstractColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/AbstractColumnType.php),
you'll see that `getParent()` method returns fully-qualified name of the [`ColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ColumnType.php) type class.
This is the type that defines all the basic options, such as `attr`, `label`, etc.
:::

### Rendering the column type

Because our phone number column is based off the built-in [`TextColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/TextColumnType.php),
it will be rendered as a text as long as the `PhoneNumber` object can be cast to string. However, in our case, let's store this logic in the template. 

First, create a custom theme for the data table, and create a `column_phone_number_value` block:

```twig
{# templates/data_table/theme.html.twig #}

{% block column_phone_number_value %}
    +{{ value.countryCode }} {{ value.nationalNumber }}
{% endblock %}
```

The block naming follows a set of rules:

- for columns, it always starts with `column` prefix;
- next comes the block prefix of the column type;
- last part of the block name represents a part of the column. The column is split into multiple parts when rendering:
  - `label` - displayed in the column header and in [personalization](../features/personalization.md) column list;
  - `header` - displayed at the top of the column, allows [sorting](../features/sorting.md) if the column is sortable;
  - `value` - like shown in example above, it renders the value itself;

If you take a look at the [`AbstractColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/AbstractColumnType.php),
you'll see that `getBlockPrefix()` returns snake cased short name of the type class, without the `ColumnType` suffix.

In our case, because the type class is named `PhoneNumberColumnType`, the default block prefix equals `phone_number`. Simple as that.

Now, the custom theme should be added to the bundle configuration:

::: code-group

```yaml [YAML]
kreyu_data_table:
  defaults:
    themes:
      # ...
      - 'data_table/theme.html.twig'
```

```php [PHP]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->defaults()->themes([
        // ...
        'data_table/theme.html.twig',
    ]);
};
```

:::


If the `column_phone_number_value` block wasn't defined in any of the configured themes, the bundle will render block of the parent type.
In our example, because we set [`TextColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/TextColumnType.php) as a parent, a `column_text_value` block will be rendered.

### Adding configuration options

Column type options allow to configure the behavior of the column types.
The options are defined in the `configureOptions()` method, using the [OptionsResolver component](https://symfony.com/doc/current/components/options_resolver.html).

Imagine, that you want to determine whether the country code should be rendered. This could be achieved by using a `show_country_code` option:

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\AbstractColumnType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneNumberColumnType extends AbstractColumnType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            // define available options and their default values
            ->setDefaults([
                'show_country_code' => true,
            ])
            // optionally you can restrict type of the options
            ->setAllowedTypes('country_code', 'bool')
        ;
    }
}
```

Now you can configure the new option when using the column type:

```php
class UserDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            // ...
            ->addColumn('phone', PhoneNumberColumnType::class, [
                'show_country_code' => false,
            ])
        ;
    }
}
```

### Passing variables to the template

Now, the `show_country_code` option is defined, but is not utilized by the system in any way.
In our case, we'll pass the options to the view, and use them to render the template itself:

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\AbstractColumnType;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;

class PhoneNumberColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars['show_country_code'] = $options['show_country_code'];
    }
}
```

Now we can update the template of the type class to use the newly added variable:

```twig
{# templates/data_table/theme.html.twig #}

{% block column_phone_number_value %}
    {% if show_country_code %}
        +{{ value.countryCode }}
    {% endif %}
    
    {{ value.nationalNumber }}
{% endblock %}
```

## Column type extensions

Column type extensions allows modifying configuration of the existing column types, even the built-in ones.
Let's assume, that we want to add a `trim` option, which will automatically apply the PHP `trim` method
on every column type in the system that uses [`TextColumnType`](../../reference/types/column/text.md) as its parent. 

Column type extensions are classes that implement [`ColumnTypeExtensionInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Extension/ColumnTypeExtensionInterface.php). 
However, it's better to extend from the [`AbstractColumnTypeExtension`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Extension/AbstractColumnTypeExtension.php):

```php
use Kreyu\Bundle\DataTableBundle\Column\Extension\AbstractColumnTypeExtension;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrimColumnTypeExtension extends AbstractColumnTypeExtension
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $value = $view->vars['value'];
        
        if (!$options['trim'] || !is_string($value)) {
            return;
        }
        
        $view->vars['value'] = trim($value);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('trim', true)
            ->setAllowedTypes('country_code', 'bool')
        ;
    }
    
    public static function getExtendedTypes(): iterable
    {
        return [TextColumnType::class];
    }
}
```

Now, automatically, the [`TextColumnType`](../../reference/types/column/text.md) type, as well as every other type that uses it as a parent, have a `trim` option available,
and its value is trimmed based on this option.

If your extension aims to cover every column type in the system, provide the base [`ColumnType`](../../reference/types/column/column.md) in the `getExtendedTypes()` method.  
