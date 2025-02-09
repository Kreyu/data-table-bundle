# Actions

[[toc]]

## Prerequisites

There are three contexts that the action can be defined with:

**Regular actions**

Regular actions are not bound to any data, and are displayed on top of the data table.
This kind of action can be used, for example, for "Create" button that redirects to a form.

**Row actions**

Actions that are bound to a row, displayed in an "actions" column.
This kind of action can be used, for example, for "Update" button that redirects to edit form for a record.

**Batch actions**

Actions that are bound to a multiple rows, selected by a checkbox column. 
Batch actions require `batch` Stimulus controller enabled: 

```json5
// assets/controllers.json
{
    "controllers": {
        "@kreyu/data-table-bundle": {
            // ...
            "batch": {
                "enabled": true
            }
        }
    }
}
```

## Adding actions

Actions can be added by using a data table builder's `addAction()`, `addRowAction()` and `addBatchAction()` methods:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addAction('create', ButtonActionType::class, [
                'href' => $this->urlGenerator->generate('app_product_create'),
            ])
            // note that row action has access to a row data in a callable
            ->addRowAction('update', ButtonActionType::class, [
                'href' => function (Product $product) {
                    return $this->urlGenerator->generate('app_product_update', [
                        'id' => $product->getId(),                
                    ]);
                }
            ])
            ->addBatchAction('delete', ButtonActionType::class, [
                'href' => $this->urlGenerator->generate('app_product_batch_delete'),
            ])
        ;
    }
}
```

Those methods accept _three_ arguments:

- action name;
- action type — with a fully qualified class name;
- action options — defined by the action type, used to configure the action;

For reference, see [available action types](../../reference/types/action.md).

## Creating action types

If built-in action types are not enough, you can create your own. In following chapters, we'll be creating an action that opens a modal. 

Action types are classes that implement [ActionTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ActionTypeInterface.php), although, it is recommended to extend from the [AbstractActionType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/AbstractActionType.php) class:

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\AbstractActionType;

class ModalActionType extends AbstractActionType
{
}
```

<div class="tip custom-block" style="padding-top: 8px;">

Recommended namespace for the action type classes is `App\DataTable\Action\Type\`.

</div>

### Action type inheritance

Because our modal action fundamentally renders as a button, let's base it off the built-in [`ButtonActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ButtonActionType.php).
Provide the fully-qualified class name of the parent type in the `getParent()` method:

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\AbstractActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

class ModalActionType extends AbstractActionType
{
    public function getParent(): ?string
    {
        return ButtonActionType::class;
    }
}
```

::: tip
If you take a look at the [`AbstractActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/AbstractActionType.php), 
you'll see that `getParent()` method returns fully-qualified name of the [`ActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ActionType.php) type class.
This is the type that defines all the basic options, such as `attr`, `label`, etc.
:::

### Rendering the action type

Because our modal action is based off the built-in [`ButtonActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ButtonActionType.php),
it will be rendered as a button without any additional configuration. However, in our case, we want to add the modal itself.

First, create a custom theme for the data table, and create a `action_modal_value` block:

```twig
{# templates/data_table/theme.html.twig #}

{% block action_modal_value %}
    <button class="btn btn-primary" data-bs-toggle="modal", data-bs-target="#action-modal-{{ name }}">
        {{ label }}
    </button>
    
    <div class="modal fade" id="#action-modal-{{ name }}">
        {# Bootstrap modal contents... #}
    </div>
{% endblock %}
```

The block naming follows a set of rules:

- for actions, it always starts with `action_` prefix;
- next comes the block prefix of the action type;
- last part is always the `_value` suffix;

If you take a look at the [`AbstractActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/AbstractActionType.php),
you'll see that `getBlockPrefix()` returns snake cased short name of the type class, without the `ActionType` suffix.

In our case, because the type class is named `ModalActionType`, the default block prefix equals `modal`. Simple as that.

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

If the `action_modal_value` block wasn't defined in any of the configured themes, the bundle will render block of the parent type.
In our example, because we set [`ButtonActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ButtonActionType.php) as a parent, a `action_button_value` block will be rendered.

### Adding configuration options

Action type options allow to configure the behavior of the action types.
The options are defined in the `configureOptions()` method, using the [OptionsResolver component](https://symfony.com/doc/current/components/options_resolver.html).

Imagine, that you want to provide a template to render as the action modal contents.
The template could be provided by a custom `template_path` and `template_vars` options:

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\AbstractActionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModalActionType extends AbstractActionType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            // define options required by the type
            ->setRequired('template_path')
            // define available options and their default values
            ->setDefaults([
                'template_vars' => [],
            ])
            // optionally you can restrict type of the options
            ->setAllowedTypes('template_path', 'string')
            ->setAllowedTypes('template_vars', 'array')
        ;
    }
}
```

Now you can configure the new option when using the action type:

```php
class UserDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            // ...
            ->addRowAction('details', ModalActionType::class, [
                'template_path' => 'user/details.html.twig',
            ])
        ;
    }
}
```

### Passing variables to the template

Now, the `template_path` and `template_vars` options are defined, but are not utilized by the system in any way.
In our case, we'll pass the options to the view, and use them to render the template itself:

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;

class ModalActionType extends ButtonActionType
{
    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        $view->vars['template_path'] = $options['template_path'];
        $view->vars['template_vars'] = $options['template_vars'];
    }
}
```

Now we can update the template of the type class to use the newly added variables:

```twig
{# templates/data_table/theme.html.twig #}

{% block action_modal_value %}
    <button class="btn btn-primary" data-bs-toggle="modal", data-bs-target="#action-modal-{{ name }}">
        {{ label }}
    </button>
    
    <div class="modal fade" id="action-modal-{{ name }}">
        <div class="modal-dialog">
            <div class="modal-content">
                {{ include(template_path, template_vars) }} // [!code ++]
            </div>
        </div>
    </div>
{% endblock %}
```

### Using row data in options

> What if I want to pass an option based on the row data?

If the action type is used for a row action, the `ActionView` parent will be an instance of `ColumnValueView`,
which can be used to retrieve a data of the row. This can be used in combination with accepting the `callable` options:

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView; // [!code ++] 

class ModalActionType extends ButtonActionType
{
    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        if ($view->parent instanceof ColumnValueView) { // [!code ++] 
            $value = $view->parent->vars['value']; // [!code ++] 

            foreach (['template_path', 'template_vars'] as $optionName) { // [!code ++] 
                if (is_callable($options[$optionName])) { // [!code ++] 
                    $options[$optionName] = $options[$optionName]($value); // [!code ++] 
                } // [!code ++]
            } // [!code ++]
        } // [!code ++]
    
        $view->vars['template_path'] = $options['template_path'];
        $view->vars['template_vars'] = $options['template_vars'];
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            // define options required by the type
            ->setRequired('template_path')
            // define available options and their default values
            ->setDefaults([
                'template_vars' => [],
            ])
            // optionally you can restrict type of the options
            ->setAllowedTypes('template_path', 'string') // [!code --]
            ->setAllowedTypes('template_path', ['string', 'callable']) // [!code ++]
            ->setAllowedTypes('template_vars', 'array') // [!code --]
            ->setAllowedTypes('template_vars', ['array', 'callable']) // [!code ++]
        ;
    }
}
```

Now, you can use the `callable` options when defining the modal row action: 

```php
class UserDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            // ...
            ->addRowAction('details', ModalActionType::class, [
                'template_path' => 'user/details.html.twig',
                'template_vars' => function (User $user) { // [!code ++]
                    return [ // [!code ++]
                        'user_id' => $user->getId(), // [!code ++]
                    ]; // [!code ++]
                }, // [!code ++]
            ])
        ;
    }
}
```

## Action type extensions

Action type extensions allows modifying configuration of the existing action types, even the built-in ones.
Let's assume, that we want to add a [Bootstrap tooltip](https://getbootstrap.com/docs/5.3/components/tooltips/#overview) for every button action, as long as their `title` attribute is defined.

Action type extensions are classes that implement [`ActionTypeExtensionInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Extension/ActionTypeExtensionInterface.php).
However, it's better to extend from the [`AbstractActionTypeExtension`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Extension/AbstractActionTypeExtension.php):

```php
use Kreyu\Bundle\DataTableBundle\Action\Extension\AbstractActionTypeExtension;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TooltipActionTypeExtension extends AbstractActionTypeExtension
{
    public function buildValueView(ActionValueView $view, ActionInterface $column, array $options): void
    {
        if (!$options['tooltip']) {
            return;
        }
        
        $title = $view->vars['attr']['title'] ?? null;
        
        if (empty($title)) {
            return;
        }
        
        $view->vars['attr']['data-bs-toggle'] = 'tooltip';
        $view->vars['attr']['data-bs-placement'] = 'top';
        $view->vars['attr']['data-bs-title'] = $title;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('tooltip', true)
            ->setAllowedTypes('tooltip', 'bool')
        ;
    }
    
    public static function getExtendedTypes(): iterable
    {
        return [ButtonActionType::class];
    }
}
```

Now, as long as the button action `tooltip` option equals to `true` (by default), and a `title` attribute is set,
the action will be rendered with Bootstrap tooltip attributes. You can even use the action name instead of the `title` attribute!

## Adding action confirmation

Actions can be configured to require confirmation (by the user) before being executed.

![Action confirmation modal with the Tabler theme](/action_confirmation_modal.png)

To enable action confirmation, set its `confirmation` option to `true`:

```php
$builder->addRowAction('remove', ButtonActionType::class, [
    'confirmation' => true,
]);
```

To configure the confirmation modal, pass the array as the `confirmation` option:

```php
$builder->addRowAction('remove', ButtonActionType::class, [
    'confirmation' => [
        'translation_domain' => 'KreyuDataTable',
        'label_title' => 'Action confirmation',
        'label_description' => 'Are you sure you want to execute this action?',
        'label_confirm' => 'Confirm',
        'label_cancel' => 'Cancel',
        'type' => 'danger', // "danger", "warning" or "info"
    ],
]);
```

For reference, see details about the [`confirmation`](#) option.

## Conditionally rendering the action

Action visibility can be configured using its [`visible`](#) option:

```php
$builder->addRowAction('remove', ButtonActionType::class, [
    'visible' => $this->isGranted('ROLE_ADMIN'),
]);
```

Another approach would be simply not adding the action at all:

```php
if ($this->isGranted('ROLE_ADMIN')) {
    $builder->addRowAction('remove', ButtonActionType::class);
}
```

What differentiates those two methods, is that by using the `visible` option, the action is still defined in the data table, but is not rendered in the view.
It may be useful in some cases, for example, when the actions can be modified outside the data table builder.

## Batch action specifics

### Adding checkbox column

Batch actions require the user to select specific rows. This is handled by the [`CheckboxColumnType`](../../reference/types/column/checkbox.md),
which simply renders a checkbox with value set to row identifier. To help with that process,
if at least one batch action is defined, this checkbox column will be added automatically.

This column will be named `__batch`, which can be referenced using the constant:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;

$column = $builder->getColumn(DataTableBuilderInterface::BATCH_CHECKBOX_COLUMN_NAME);
```

This behavior can be disabled (or enabled back again) using the builder's method:

```php
$builder->setAutoAddingBatchCheckboxColumn(false);
```

### Changing identifier parameter name

By default, the checkbox column type will add the `id` parameter to the batch actions.
For example, checking rows with ID 1, 2 will result in:

- every batch action's `href` parameter appended with `id[]=1&id[]=2`
- every batch action's `data-id` parameter set to `[1,2]`

The parameter name can be changed by providing the `identifier_name` option:

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\CheckboxColumnType;

$builder->addColumn('__batch', CheckboxColumnType::class, [
    'identifier_name' => 'product_id',
]);
```

Using the above configuration, checking rows with ID 1, 2 will result in:

- every batch action's `href` parameter appended with `product_id[]=1&product_id[]=2`
- every batch action's `data-product-id` parameter set to `[1,2]`

If the action has no `href` parameter, the query parameters will not be appended.
The data parameters are not used internally and can be used for custom scripts.

If `FormActionType` is used, the scripts will append hidden inputs with selected values, for example:

```html
<input type="hidden" name="id[]" value="1">
<input type="hidden" name="id[]" value="2">
```

### Changing identifier parameter value

By default, the checkbox column type will try to retrieve the identifier on the `id` property path.
This can be changed similarly to other column types, by providing the `property_path` option:

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\CheckboxColumnType;

$builder->addColumn('__batch', CheckboxColumnType::class, [
    'property_path' => 'uuid',
]);
```

If property accessor is not enough, use the `getter` option:

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\CheckboxColumnType;

$builder->addColumn('__batch', CheckboxColumnType::class, [
    'getter' => fn (Product $product) => $product->getUuid(),
]);
```

### Multiple checkbox columns

Using multiple checkbox columns for a single data table is supported.
For example, using the following configuration:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Column\Type\CheckboxColumnType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('productId', CheckboxColumnType::class, [
                'property_path' => 'id',
                'identifier_name' => 'product_id',
            ])
            ->addColumn('categoryId', CheckboxColumnType::class, [
                'property_path' => 'category.id',
                'identifier_name' => 'category_id',
            ])
        ;
    }
}
```

And having a data set which consists of two rows:

| Product ID | Category ID |
|------------|-------------|
| 1          | 3           |
| 2          | 4           |

Checking the first row's product and second row's category will result in:

- every batch action's `href` parameter appended with `product_id[]=1&category_id[]=4`
- every batch action's `data-product-id` parameter set to `[1]` and `data-category-id` set to `[4]`

If the action has no `href` parameter, the query parameters will not be appended.
The data parameters are not used internally and can be used for custom scripts.

If `FormActionType` is used, the scripts will append hidden inputs with selected values, for example:

```html
<input type="hidden" name="product_id[]" value="1">
<input type="hidden" name="category_id[]" value="4">
```

## Dropdown actions

In some cases, it may be useful to group multiple actions under a single dropdown.

To do so, define an action using the [`DropdownActionType`](../../reference/types/action/dropdown.md) type:
Then, define child actions under its `actions` array. Each child action can be created using the builder's `createAction`, `createRowAction` or `createBatchAction` method, depending on the context:

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\DropdownActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\LinkDropdownItemActionType;

$builder
    ->addRowAction('advanced', DropdownActionType::class, [
        'actions' => [
            $builder->createRowAction('update', LinkDropdownItemActionType::class, [
                'href' => function (Post $post) {
                    return $this->urlGenerator->generate('post_delete', [
                        'id' => $post->getId(),
                    ]),
                },
            ]),
        ],
    ])
;
```

> [!TIP]
> Although any action type can be used, rendering forms and buttons inside a dropdown may look weird.
> Therefore, it is recommended to use [`LinkDropdownItemActionType`](../../reference/types/action/link-dropdown-item.md) for dropdown items,
> so it will be rendered properly as a simple link.

## Modal actions

You can define an action to open a modal with contents loaded from given URL.
For example, let's define a modal that displays a post details.

First things first, the built-in modal action type requires additional JavaScript to work properly.
If using the built-in Bootstrap 5 or Tabler (based on Bootstrap) theme, we have to enable the `bootstrap-modal` 
script in your `controllers.json` file, because **it is disabled by default**:

```json
{
  "controllers": {
    "@kreyu/data-table-bundle": {
      "bootstrap-modal": {
        "enabled": true
      }
    }
  }
}
```

Then, simply add a row action using the `ModalActionType`:

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ModalActionType;

$builder
    ->addRowAction('details', ModalActionType::class, [
        'route' => 'post_details_modal',
        'route_params' => fn (Post $post) => [
            'id' => $post->getId(),
        ],
        // You can generate the URL by yourself with "href" option
        // instead of using "route" and "route_params":
        //
        // 'href' => function (Post $post) {
        //     return $this->urlGenerator->generate('post_details_modal', [
        //         'id' => $post->getId(),
        //     ]);
        // },
    ])
;
```

Now, make sure the `post_details_modal` route is defined in a controller:

```php
#[Route('/posts/{id}/details', name: 'post_details_modal')]
public function details(Post $post): Response
{
    return $this->render('posts/details_modal.html.twig', [
        'post' => $post,
    ]);
}
```

Inside the template, we can render the modal however we want:

```twig
{# templates/posts/details_modal.html.twig #}
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-body">
            <h5 class="modal-title">{{ post.title }}</h5>
            <p>{{ post.content }}</p>
        </div>
    </div>
</div>
```

For more details, see the [`ModalActionType`](../../reference/types/action/modal.md) reference page.

## Refresh actions

You have the option to add an action that refreshes the content of the DataTable.

Thanks to `Hotwire Turbo`, only the content of the DataTable is refreshed.

This action cannot be added as a `RowAction` or `BatchAction`.