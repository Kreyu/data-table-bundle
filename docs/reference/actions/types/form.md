# FormActionType

The [:material-github: FormActionType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/FormActionType.php) represents an action displayed as a button wrapped in form.

## Options

### `action`

**type**: `string` or `callable` **default**: `'#'`

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

**type**: `string` or `callable` **default**: `'GET'`

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

**type**: `array` **default**: `[]`

If you want to add extra attributes to an HTML representation of the form's submit button, you can use the button_attr option.
It's an associative array with HTML attributes as keys. 

### `icon_attr`

**type**: `array` **default**: `[]`

If you want to add extra attributes to an HTML representation of the button's icon, you can use the icon_attr option.
It's an associative array with HTML attributes as keys.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\FormActionType;

$builder
    ->addAction('send', FormActionType::class, [
        'icon_attr' => [
            'class' => 'fa fa-envelope',
        ],
    ])
;
```

## Inherited options

{% include-markdown "_action_options.md" heading-offset=2 %}
