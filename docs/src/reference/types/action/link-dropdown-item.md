<script setup>
    import ActionTypeOptions from "./options/action.md";
    import TurboPrefetchingSection from "./../../../shared/turbo-prefetching.md";
</script>

# LinkDropdownItemActionType

The [`LinkDropdownItemActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/Dropdown/LinkDropdownItemActionType.php) 
represents an action rendered as dropdown item with a simple link.  It is meant to be used as a child of the [`DropdownActionType`](dropdown.md).

## Prefetching

<TurboPrefetchingSection>

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\DropdownActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\LinkDropdownItemActionType;

$builder
    ->addRowAction('advanced', DropdownActionType::class, [
        'actions' => [
            $builder->createAction('show', LinkDropdownItemActionType::class, [
                'attr' => [
                    // note that this "false" should be string, not a boolean
                    'data-turbo-prefetch' => 'false',
                ],
            ]),
        ],
    ])
;
```

</TurboPrefetchingSection>

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
                'href' => '#',           
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
                'href' => function (Post $post) {
                    return $this->urlGenerator->generate('post_update', [
                        'id' => $post->getId(),
                    ]);
                },
            ]),
        ],
    ])
;
```

### `target`

- **type**: `string` or `callable` (if using as a row action)
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

When using the `LinkActionType` as a [row action](../../../docs/components/actions.md), you can provide a callable
that will receive the row data as an argument and should return a string.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\DropdownActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\LinkDropdownItemActionType;

$builder
    ->addAction('advanced', DropdownActionType::class, [
        'actions' => [
            $builder->createRowAction('wiki', LinkDropdownItemActionType::class, [
                'target' => function (Configuration $configuration) {
                    return $configuration->isExternal() ? '_blank' : '_self';
                },
            ])
        ],
    ])
    
;
```

## Inherited options

<ActionTypeOptions/>
