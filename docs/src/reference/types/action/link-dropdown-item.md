<script setup>
    import ActionTypeOptions from "./options/action.md";
</script>

# LinkDropdownItemActionType

The [`LinkDropdownItemActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/Dropdown/LinkDropdownItemActionType.php) 
represents an action rendered as dropdown item with a simple link.  It is meant to be used as a child of the [`DropdownActionType`](dropdown.md).

## Options

### `href`

- **type**: `string` or `callable` (if using as a row action)
- **default**: `'#'`

A value used as an action link [href attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-href).

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\DropdownActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\LinkDropdownItemActionType;

$builder
    ->addAction('advanced', DropdownActionType::class, [
        'actions' => [
            $builder->createAction('update', LinkDropdownItemActionType::class, [
                'href' => fn (Post $post) => $this->urlGenerator->generate('post_update', [
                    'id' => $post->getId(),
                ]),           
            ]),
        ],
    ])
;
```

When using the `LinkDropdownItemActionType` as a [row action](../../../docs/components/actions.md), you can provide a callable
that will receive the row data as an argument and should return an array of actions.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\DropdownActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\LinkDropdownItemActionType;

$builder
    ->addRowAction('advanced', DropdownActionType::class, [
        'actions' => [
            $builder->createRowAction('update', LinkDropdownItemActionType::class, [
                'href' => fn (Post $post) => $this->urlGenerator->generate('post_update', [
                    'id' => $post->getId(),
                ]),            
            ]),
        ],
    ])
;
```

### `target`

- **type**: `string` or `callable`
- **default**: `'_self'`

Sets the value that will be used as an anchor [target attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-target).

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\DropdownActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\LinkDropdownItemActionType;

$builder
    ->addAction('preview', DropdownActionType::class, [
        'actions' => [
            $builder->createAction('render', LinkDropdownItemActionType::class, [
                'href' => '#',
                'target' => '_blank',
            ]),
        ],
    ])
;
```

## Inherited options

<ActionTypeOptions/>
