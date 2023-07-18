### `label`

- **type**: `string` or `Symfony\Component\Translation\TranslatableMessage` 
- **default**: the label is "guessed" from the action name

A label representing the action.

### `label_translation_parameters`

- **type**: `array`
- **default**: `[]`

An array of parameters used to translate the action label.

### `translation_domain`

- **type**: `false` or `string` 
- **default**: the default `KreyuDataTable` is used

Translation domain used in translation of action's translatable values.

### `block_prefix`

- **type**: `string` 
- **default**: action type block prefix

Allows you to add a custom block prefix and override the block name used to render the action type.
Useful, for example, if you have multiple instances of the same action type, and you need to personalize
the rendering of some of them, without the need to create a new action type.

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

### `icon_attr`

- **type**: `array`
- **default**: `[]`

An array of attributes used to render the action's icon.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

$builder
    ->addAction('remove', ButtonActionType::class, [
        'icon_attr' => [
            'class' => 'ti ti-trash',
        ],
    ])
;
```

### `confirmation`

- **type**: `bool`, `array` or `callable`
- **default**: `false`

Determines whether the action is confirmable, which displays a modal where user have to acknowledge the process.
The modal can be configured by passing an array with the following options:

#### `translation_domain`

- **type**: `false` or `string`
- **default**: `'KreyuDataTable'`

Translation domain used in translation of action confirmation labels.

#### `label_title`

- **type**: `null` or `string`
- **default**: `'Action confirmation'` 

#### `label_description`

- **type**: `null` or `string`
- **default**: `'Are you sure you want to execute this action?'`

#### `label_confirm`

- **type**: `null` or `string`
- **default**: `'Confirm'`

#### `label_cancel`

- **type**: `null` or `string`
- **default**: `'Cancel'`

#### `type`

- **type**: `null` or `string`
- **default**: `danger`
- **allowed values**: `danger`, `warning`, `info`

Represents a type of the action confirmation, which determines the color of the displayed modal.
