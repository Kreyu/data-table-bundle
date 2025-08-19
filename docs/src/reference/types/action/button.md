<script setup>
    import ActionTypeOptions from "./options/action.md";
    import TurboPrefetchingSection from "./../../../shared/turbo-prefetching.md";
</script>

# ButtonActionType

The [`ButtonActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ButtonActionType.php) represents an action rendered as a button.

## Prefetching

<TurboPrefetchingSection>

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

$builder->addRowAction('show', ButtonActionType::class, [
    'attr' => [
        // note that this "false" should be string, not a boolean
        'data-turbo-prefetch' => 'false',
    ],
]);
```

</TurboPrefetchingSection>

## Options

### `href`

- **type**: `string` or `\Closure`
- **default**: `'#'`

A value used as an action link [href attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-href).

```php #
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

$builder
    ->addAction('back', ButtonActionType::class, [
        'href' => $this->urlGenerator->generate('category_index'),
    ])
;
```

### `target`

- **type**: `string` or `\Closure`
- **default**: `'_self'`

Sets the value that will be used as an anchor [target attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-target).

```php #
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;

$builder
    ->addAction('wiki', ButtonActionType::class, [
        'target' => '_blank',
    ])
;
```

## Inherited options

<ActionTypeOptions/>
