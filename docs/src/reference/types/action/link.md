<script setup>
    import ActionTypeOptions from "./options/action.md";
</script>

# LinkActionType

The [`LinkActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/LinkActionType.php) represents an action rendered as a simple link.

## Options

### `href`

- **type**: `string` or `callable`
- **default**: `'#'`

A value used as an action link [href attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-href).

```php #
use Kreyu\Bundle\DataTableBundle\Action\Type\LinkActionType;

$builder
    ->addAction('back', LinkActionType::class, [
        'href' => $this->urlGenerator->generate('category_index'),
    ])
;
```

### `target`

- **type**: `string` or `callable`
- **default**: `'_self'`

Sets the value that will be used as an anchor [target attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-target).

```php #
use Kreyu\Bundle\DataTableBundle\Action\Type\LinkActionType;

$builder
    ->addAction('wiki', LinkActionType::class, [
        'target' => '_blank',
    ])
;
```

## Inherited options

<ActionTypeOptions/>
