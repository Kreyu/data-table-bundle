<script setup>
    import ActionTypeOptions from "./options/action.md";
</script>

# LinkActionType

The [`LinkActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/LinkActionType.php) represents an action rendered as a simple link.

## Options

### `href`

- **type**: `string` or `callable` (if using as a row action)
- **default**: `'#'`

A value used as an action link [href attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-href).

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\LinkActionType;

$builder
    ->addAction('back', LinkActionType::class, [
        'href' => $this->urlGenerator->generate('category_index'),
    ])
;
```

When using the `LinkActionType` as a [row action](../../../docs/components/actions.md), you can provide a callable
that will receive the row data as an argument and should return a string.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\LinkActionType;

$builder
    ->addAction('back', LinkActionType::class, [
        'href' => function (Category $category) {
            return $this->urlGenerator->generate('category_index', [
                'id' => $category->getId(),        
            ]);
        },
    ])
;
```

### `target`

- **type**: `string` or `callable` (if using as a row action)
- **default**: `'_self'`

Sets the value that will be used as an anchor [target attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-target).

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\LinkActionType;

$builder
    ->addAction('wiki', LinkActionType::class, [
        'target' => '_blank',
    ])
;
```

When using the `LinkActionType` as a [row action](../../../docs/components/actions.md), you can provide a callable
that will receive the row data as an argument and should return a string.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\LinkActionType;

$builder
    ->addRowAction('wiki', LinkActionType::class, [
        'target' => function (Configuration $configuration) {
            return $configuration->shouldOpenNewTab() ? '_blank' : '_self';
        },
    ])
;
```

## Inherited options

<ActionTypeOptions/>
