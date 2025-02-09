<script setup>
    import ActionTypeOptions from "./options/action.md";
</script>

# DropdownActionType

The [`DropdownActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/Dropdown/DropdownActionType.php) represents an action rendered as a dropdown, where each item corresponds to separate action.

## Options

### `actions`

- **type**: `array` or `callable` (if using as a row action)

An array of actions that will be rendered as dropdown items.
Each action can be created using `createAction`, `createRowAction` or `createBatchAction` method, depending on the context:

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\DropdownActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\LinkDropdownItemActionType;

$builder
    ->addAction('advanced', DropdownActionType::class, [
        'actions' => [
            $builder->createAction('update', LinkDropdownItemActionType::class, [
                'href' => '#'            
            ]),
        ],
    ])
;
```

While theoretically you _can_ use wrong method for dropdown items, e.g. `createBatchAction` for a dropdown action created by `addRowAction`,
the bundle will automatically change the context of the action to the proper one. However, try to use proper methods for better readability.

When using the `DropdownActionType` as a [row action](../../../docs/components/actions.md), you can provide a callable
that will receive the row data as an argument and should return an array of actions.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\DropdownActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\LinkDropdownItemActionType;

$builder
    ->addRowAction('advanced', DropdownActionType::class, [
        'actions' => fn (Post $post) => [
            $builder->createRowAction('update', LinkDropdownItemActionType::class, [
                'href' => $this->urlGenerator->generate('post_update', [
                    'id' => $post->getId(),
                ]),
            ]),
        ],
    ])
;
```

> [!TIP]
> Although any action type can be used, rendering forms and buttons inside a dropdown may look weird.
> Therefore, it is recommended to use [`LinkDropdownItemActionType`](link-dropdown-item.md) for dropdown items,
> so it will be rendered properly as a simple link.

### `with_caret`

- **type**: `bool`
- **default**: `true`

Whether to render a caret icon next to the dropdown label. For example:

![Dropdown action with and without caret example](./dropdown_action_type_with_caret_example.png)

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\DropdownActionType;

$builder 
    ->addRowAction('dropdownWithCaret', DropdownActionType::class, [
        'label' => 'Dropdown with caret',
        'with_caret' => true,
        'actions' => [...],
    ])
    ->addRowAction('dropdownWithoutCaret', DropdownActionType::class, [
        'label' => '···',
        'with_caret' => false,
        'actions' => [...],
    ])
;
```

## Inherited options

<ActionTypeOptions/>
