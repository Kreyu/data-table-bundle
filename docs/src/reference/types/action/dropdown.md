<script setup>
    import ActionTypeOptions from "./options/action.md";
</script>

# DropdownActionType

The [`DropdownActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/DropdownActionType.php) represents an action rendered as a dropdown, where each item corresponds to separate action.

## Options

### `actions`

- **type**: `array` or `callable` (if using as a row action)
- **default**: `[]`

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

## Inherited options

<ActionTypeOptions/>
