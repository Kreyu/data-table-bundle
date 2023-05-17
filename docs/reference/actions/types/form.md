---
label: Form
visibility: hidden
order: c
---

# Form action type

The `FormActionType` represents an action displayed as a button.

+-------------+---------------------------------------------------------------------+
| Parent type | [ActionType](action.md)
+-------------+---------------------------------------------------------------------+
| Class       | [:icon-mark-github: FormActionType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/FormActionType.php)
+-------------+---------------------------------------------------------------------+

## Options

### `action`

- **type**: `string` or `callable` 
- **default**: `'#'`

Sets the value that will be used as a form's `action` attribute.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\FormActionType;

$builder
    ->addAction('send', FormActionType::class, [
        'action' => $this->urlGenerator->generate('sms_send'),
    ])
;
```

### `method`

- **type**: `string` or `callable` 
- **default**: `'GET'`

Sets the value that will be used as a form's `method` attribute.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\FormActionType;

$builder
    ->addAction('send', FormActionType::class, [
        'method' => 'POST',
    ])
;
```

### `button_attr`

- **type**: `array` 
- **default**: `[]`

An array of attributes used to render the form submit button.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

$builder
    ->addAction('remove', ButtonActionType::class, [
        'attr' => [
            'class' => 'btn btn-danger',
        ],
    ])
;
```

## Inherited options

{{ include '_action_options' }}
