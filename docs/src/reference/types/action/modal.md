<script setup>
    import ActionTypeOptions from "./options/action.md";
</script>

# ModalActionType

The [`ModalActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ModalActionType.php) represents an action
that opens a modal dialog with contents loaded from given URL.

The trigger is a link (`<a href>`). Clicking it opens the modal and loads the URL into it via the `dialog` Stimulus controller.
As a batch action, the link's `href` gets the selected identifiers appended (`?id[]=1&id[]=2`).

What the URL should return depends on the theme:

| Theme | Modal | Response |
|-------|-------|----------|
| Base | native `<dialog>` | dialog body content |
| Bootstrap 5, Tabler | Bootstrap modal | full `.modal-dialog` markup |

> [!WARNING]
> Requires the `dialog` controller in your `controllers.json`. It's enabled by default; if your file predates it, add it:
>
> ```json
> {
>   "controllers": {
>     "@kreyu/data-table-bundle": {
>       "dialog": {
>         "enabled": true,
>         "fetch": "eager"
>       }
>     }
>   }
> }
> ```
>
> The `bootstrap-modal` controller is deprecated. Only themes that copied the old Bootstrap 5 `action_modal_control` block still use it.

## Options

### `href`

- **type**: `null`, `string` or `\Closure` (if using as a row action)
- **default**: `null`

A URL to load the modal contents from. Can be used instead of [`route`](#route) and [`route_params`](#route_params) options,
so in most cases this would require to inject the URL generator service:

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ModalActionType;

$builder
    ->addAction('help', ModalActionType::class, [
        'href' => $this->urlGenerator->generate('post_help_modal');
    ])
;
```

When using the `ModalActionType` as a [row action](../../../docs/components/actions.md), you can provide a closure
that will receive the row data as an argument and should return a URL.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ModalActionType;

$builder
    ->addRowAction('details', ModalActionType::class, [
        'href' => fn (Post $post) => [
            $this->urlGenerator->generate('post_details_modal', [
                'id' => $post->getId(),
            ]),
        ],
    ])
;
```

> [!TIP]
> You can use [`route`](#route) and [`route_params`](#route_params) options instead of manually injecting the URL generator service.
> Setting both the `href` and `route` options simultaneously will use the URL provided in the `href` option.

### `route`

- **type**: `null`, `string` or `\Closure` (if using as a row action)
- **default**: `null`

A route name to generate the URL from. Can be used instead of [`href`](#href) option.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ModalActionType;

$builder
    ->addAction('help', ModalActionType::class, [
        'route' => 'post_help_modal',
    ])
;
```

When using the `ModalActionType` as a [row action](../../../docs/components/actions.md), you can provide a closure
that will receive the row data as an argument and should return a route name.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ModalActionType;

$builder
    ->addRowAction('details', ModalActionType::class, [
        'route' => fn (Post $post) => $post->isPublished()
            ? 'post_published_details_modal'
            : 'post_draft_details_modal',
    ])
;
```

### `route_params`

- **type**: `array` or `\Closure` (if using as a row action)
- **default**: `[]`

A route params passed to the route provided in [`route`](#route) option. Can be used instead of [`href`](#href) option.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ModalActionType;

$builder
    ->addAction('help', ModalActionType::class, [
        'route' => 'post_help_modal',
        'route_params' => [
            'foo' => 'bar',
        ],
    ])
;
```

When using the `ModalActionType` as a [row action](../../../docs/components/actions.md), you can provide a closure
that will receive the row data as an argument and should return a route params array.

```php
use Kreyu\Bundle\DataTableBundle\Action\Type\ModalActionType;

$builder
    ->addRowAction('details', ModalActionType::class, [
        'route' => 'post_details_modal',
        'route_params' => fn (Post $post) => [
            'id' => $post->getId(),
        ],
    ])
;
```

## Inherited options

<ActionTypeOptions/>
