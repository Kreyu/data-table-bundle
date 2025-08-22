<script setup>
    import ActionTypeOptions from "./options/action.md";
</script>

# FormActionType

The [`FormActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/FormActionType.php) represents an action rendered as a submit button to a hidden form, which allows the action to use any HTTP method. 

## Options

### `action`

- **type**: `string` or `\Closure`
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

- **type**: `string` or `\Closure`
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

- **type**: `array` or `\Closure`
- **default**: `[]`

An array of attributes used to render the form submit button.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

$builder
    ->addAction('remove', ButtonActionType::class, [
        'button_attr' => [
            'class' => 'btn btn-danger',
        ],
    ])
;
```

## Inherited options

<ActionTypeOptions/>
