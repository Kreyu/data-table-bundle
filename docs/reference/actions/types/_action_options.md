# `label`

**type**: `string` or `Symfony\Component\Translation\TranslatableMessage` **default**: the label is "guessed" from the action name

Sets the label that will be used when rendering the action.

# `label_translation_parameters`

**type**: `array` **default**: `[]`

Sets the parameters used when translating the `label` option.

# `translation_domain`

**type**: `false` or `string` **default**: the default `KreyuDataTable` is used

Sets the translation domain used when translating the action translatable values.  
Setting the option to `false` disables translation for the action.

# `block_name`

**type**: `string` **default**: `kreyu_data_table_action_` + action type block prefix

Allows you to add a custom block name to the ones used by default to render the action type.
Useful for example if you have multiple instances of the same action type, and you need to personalize the rendering of the actions individually.

By default, if action type class name is `ButtonActionType`, the block name option will equal `kreyu_data_table_action_button`.

# `block_prefix`

**type**: `string` **default**: action type block prefix

Allows you to add a custom block prefix and override the block name used to render the action type.
Useful for example if you have multiple instances of the same action type, and you need to personalize the rendering of all of them without the need to create a new action type.

# `attr`

**type**: `array` **default**: `[]`

If you want to add extra attributes to an HTML action representation, you can use the attr option.
It's an associative array with HTML attributes as keys.
This can be useful when you need to set a custom class for some action:

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

$builder
    ->addAction('remove', ButtonActionType::class, [
        'attr' => [
            'class' => 'btn-danger',
        ],
    ])
;
```
