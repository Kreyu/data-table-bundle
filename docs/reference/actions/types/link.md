---
label: Link
visibility: hidden
order: b
---

# Link action type

The `LinkActionType` represents an action displayed as a link.

+-------------+---------------------------------------------------------------------+
| Parent type | [ActionType](action.md)
+-------------+---------------------------------------------------------------------+
| Class       | [:icon-mark-github: LinkActionType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/LinkActionType.php)
+-------------+---------------------------------------------------------------------+

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

!!! Note
The action confirmation configuration inherits value of this option as its `href`.
!!!

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

{{ include '_action_options' }}
