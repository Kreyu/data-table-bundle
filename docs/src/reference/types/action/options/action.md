### `label`

- **type**: `null`, `string` or `Symfony\Component\Translation\TranslatableInterface`
- **default**: `null`

A label representing the action.
When value equals `null`, a sentence cased action name is used as a label, for example:

| Action name  | Guessed label  |
|--------------|----------------|
| create       | Create         |
| saveAndClose | Save and close |

### `label_translation_parameters`

- **type**: `array`
- **default**: `[]`

An array of parameters used to translate the action label.

### `translation_domain`

- **type**: `false` or `string`
- **default**: `'KreyuDataTable'`

Translation domain used in translation of action's translatable values.

### `block_prefix`

- **type**: `string`
- **default**: value returned by the action type `getBlockPrefix()` method

Allows you to add a custom block prefix and override the block name used to render the action type.
Useful, for example, if you have multiple instances of the same action type, and you need to personalize
the rendering of some of them, without the need to create a new action type.

### `visible`

- **type**: `bool` or `callable`
- **default**: `true`

Determines whether the action should be visible to the user.

The callable can only be used by the row actions to determine visibility [based on the row data](../../../../docs/components/actions.md#using-row-data-in-options):

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

$builder
    ->addRowAction('remove', ButtonActionType::class, [
        'visible' => function (Product $product) {
            return $product->isRemovable();
        },
    ])
;
```

### `attr`

- **type**: `array`
- **default**: `[]`

An array of attributes used to render the action.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

$builder
    ->addAction('remove', ButtonActionType::class, [
        'attr' => [
            'class' => 'bg-danger',
        ],
    ])
;
```


### `icon`

- **type**: `null`, `string` or `callable`
- **default**: `null`

Defines the icon to render.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

$builder
    ->addAction('remove', ButtonActionType::class, [
        'icon' => 'trash',
    ])
;
```

> [!TIP] Wondering how does the icon gets rendered?
> Name of the icon depends on the icon set you are using in the application,
> and which icon theme is configured for the data table. See the [icon themes documentation section](./../../../../docs/features/theming.md#icon-themes) for more information.

When action is a [row action](./../../../../docs/components/actions.md), you can provide a callable
that will receive the row data as an argument and should return a string:

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

$builder
    ->addRowAction('toggle', ButtonActionType::class, [
        'icon' => fn (User $user) => $user->isActive() ? 'unlock' : 'lock',
    ])
;
```

### `icon_attr`

- **type**: `array` or `callable`
- **default**: `[]`

Defines the HTML attributes for the icon to render.

```php
use Kreyu\Bundle\DataTableBundle\Column\Type\IconColumnType;

$builder
    ->addColumn('status', IconColumnType::class, [
        'icon' => 'check',
        'icon_attr' => [
            'class' => 'text-success',
        ],
    ])
;
```

When action is a [row action](../../../../docs/components/actions.md), you can provide a callable
that will receive the row data as an argument and should return a string:

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

$builder
    ->addRowAction('toggle', ButtonActionType::class, [
        'icon' => fn (User $user) => $user->isActive() ? 'unlock' : 'lock',
        'icon_attr' => fn (User $user) => [
            'class' => $user->isActive() ? 'text-danger' : 'text-success',        
        ],
    ])
;
```

### `confirmation`

- **type**: `bool`, `array` or `callable`
- **default**: `false`

Determines whether the action is confirmable, which displays a modal where user have to acknowledge the process.
The modal can be configured by passing an array with the following options:

> #### `translation_domain`
> 
> - **type**: `false` or `string`
> - **default**: `'KreyuDataTable'`
> 
> #### `label_title`
> 
> - **type**: `null` or `string`
> - **default**: `'Action confirmation'`
> 
> #### `label_description`
> 
> - **type**: `null` or `string`
> - **default**: `'Are you sure you want to execute this action?'`
> 
> #### `label_confirm`
> 
> - **type**: `null` or `string`
> - **default**: `'Confirm'`
> 
> #### `label_cancel`
> 
> - **type**: `null` or `string`
> - **default**: `'Cancel'`
> 
> #### `type`
> 
> - **type**: `null` or `string`
> - **default**: `danger`
> - **allowed values**: `danger`, `warning`, `info`
> 
> Represents a type of the action confirmation, which determines the color of the displayed modal.
