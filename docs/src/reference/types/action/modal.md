<script setup>
    import ActionTypeOptions from "./options/action.md";
</script>

# ModalActionType

The [`ModalActionType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ModalActionType.php) represents an action
that opens a modal dialog with contents loaded from given URL.

> [!WARNING]
> This action type requires additional JavaScript to work properly. If using the built-in Bootstrap 5 or Tabler (based on Bootstrap) theme,
> enable the `bootstrap-modal` script in your `controllers.json` file, because **it is disabled by default**.
> 
> ```json
> {
>   "controllers": {
>     "@kreyu/data-table-bundle": {
>       "bootstrap-modal": {
>         "enabled": true
>       }
>     }
>   }
> }
> ```

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
