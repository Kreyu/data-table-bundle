# ButtonActionType

The [:material-github: ButtonActionType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ButtonActionType.php) represents an action displayed as a button.

## Options

### `href`

**type**: `string` or `callable` **default**: `'#'`

Sets the value that will be used as a link `href` attribute (see [href attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-href)).  
Closure can be used to provide an option value based on a row value, which is passed as a first argument.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\LinkActionType;

$builder
    ->addAction('show', LinkActionType::class, [
        'href' => $this->urlGenerator->generate('category_show', [
            'id' => $category->getId(),
        ]),
    ])
;
```

### `target`

**type**: `string` or `callable` **default**: `'_self'`

Sets the value that will be used as an anchor `target` attribute (see [target attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-target)).  
Closure can be used to provide an option value based on a row value, which is passed as a first argument.

### `link_attr`

**type**: `array` **default**: `[]`

If you want to add extra attributes to an HTML representation of the button's link, you can use the link_attr option.
It's an associative array with HTML attributes as keys. 

### `icon_attr`

**type**: `array` **default**: `[]`

If you want to add extra attributes to an HTML representation of the button's icon, you can use the icon_attr option.
It's an associative array with HTML attributes as keys.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

$builder
    ->addAction('remove', ButtonActionType::class, [
        'icon_attr' => [
            'class' => 'fa fa-trash',
        ],
    ])
;
```

## Inherited options

{% include-markdown "_action_options.md" heading-offset=2 %}
