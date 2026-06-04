<script setup>
    import TurboPrefetchingSection from "./../../shared/turbo-prefetching.md";
</script>

# Asynchronicity

[Symfony UX Turbo](https://symfony.com/bundles/ux-turbo/current/index.html) is a Symfony bundle integrating the [Hotwire Turbo](https://turbo.hotwired.dev/) library in Symfony applications.
It allows having the same user experience as with [Single Page Apps](https://en.wikipedia.org/wiki/Single-page_application) but without having to write a single line of JavaScript!

This bundle provides integration that works out-of-the-box.

## The magic part

Make sure your application uses the [Symfony UX Turbo](https://symfony.com/bundles/ux-turbo/current/index.html).
You don't have to configure anything extra, your data tables automatically work asynchronously!
The magic comes from the [base template](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/base.html.twig),
which wraps the whole table in the `<turbo-frame>` tag:

```twig
{# @KreyuDataTable/themes/base.html.twig #}
{% block kreyu_data_table %}
    <turbo-frame id="kreyu_data_table_{{ name }}">
        {# ... #}
    </turbo-frame>
{% endblock %}
```

This ensures every data table is wrapped in its own frame, making them work asynchronously.

<div class="tip custom-block" style="padding-top: 8px;">

This integration also works on other built-in templates, because they all extend the base one.
If you're making a data table theme from scratch, make sure the table is wrapped in the Turbo frame, as shown above.

</div>

For more information, see [official documentation about the Turbo frames](https://symfony.com/bundles/ux-turbo/current/index.html#decomposing-complex-pages-with-turbo-frames).

## Server-side responses for Turbo Frames

When a request originates from a Turbo Frame, you can return only the HTML of the data table instead of rendering the entire page. This significantly improves performance on pages with lots of content.

This bundle provides a helper for that: the `DataTableTurboResponseTrait`. It renders just the table's markup so Turbo can replace the content of the requesting frame.

How it works under the hood:
- The `HttpFoundationRequestHandler` reads the Turbo-Frame request header and stores it in the `DataTable` instance.
- The `DataTable::isRequestFromTurboFrame()` method returns true when the header matches the table frame id (`kreyu_data_table_<name>`).
- In that case, you can short-circuit the controller and return only the table HTML using the trait method.

Example controller usage:

```php
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Kreyu\Bundle\DataTableBundle\DataTableTurboResponseTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    use DataTableTurboResponseTrait;

    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $query = $productRepository->createQueryBuilder('product');

        $dataTable = $this->createDataTable(ProductDataTableType::class, $query);
        $dataTable->handleRequest($request);

        if ($dataTable->isRequestFromTurboFrame()) {
            // Return only the table's HTML so Turbo can replace the requesting <turbo-frame>
            return $this->createDataTableTurboResponse($dataTable);
        }

        // Initial (non-Turbo) request: render the full page
        return $this->render('home/index.html.twig', [
            'products' => $dataTable->createView(),
        ]);
    }
}
```

Notes:
- Make sure your table is wrapped in a `<turbo-frame>` as shown above; built-in themes already do this.
- Turbo sends the Turbo-Frame header with the frame id; the bundle reads it for you. You don't need to access headers directly.
- The trait requires Twig to be available in your controller service (it is auto-wired by Symfony via the `#[Required]` setter).

## Deferred (lazy) loading

By default, a data table queries its data while the page is being rendered. For slow data sources — or
pages with several tables — you can **defer** the data loading: the page renders immediately with a
loading placeholder, and the table data is fetched and rendered **after** the page has loaded.

Enable it with the `async` option:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    async: true
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->defaults()->async(true);
};
```

```php [For data table type]
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'async' => true,
        ]);
    }
}
```

```php [For specific data table]
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function index()
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class,
            query: $query,
            options: [
                'async' => true,
            ],
        );
    }
}
```
:::

Under the hood, the table's `<turbo-frame>` is rendered with a `src` pointing at the current URL and a
loading placeholder. Turbo then fetches that URL, and the bundle renders the real table into the frame.
On a page with multiple async tables, each frame loads independently, and only the table being fetched
runs its query.

### Controlling when the data is fetched

The `async_loading` option controls **when** Turbo fetches the deferred data:

- `lazy` (default) — the data is fetched when the table scrolls into view.
- `eager` — the data is fetched immediately after the page loads.

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    async: true
    async_loading: eager
```

```php [For data table type]
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'async' => true,
            'async_loading' => 'eager',
        ]);
    }
}
```
:::

::: warning A `lazy` table inside an initially hidden element (e.g. a `display: none` container or an
inactive tab) never enters the viewport, so it never loads. Use `async_loading: eager` for those, or
trigger the load when the element is shown.
:::

### Customizing the placeholder

The placeholder is rendered by the `async_placeholder` theme block. The base theme renders a simple
`<progress>` bar, and the Bootstrap/Tabler themes render a spinner. Override the block in your own theme
to customize it:

```twig
{% block async_placeholder %}
    <div class="my-loading-state">
        {{ 'Loading'|trans({}, 'KreyuDataTable') }}
    </div>
{% endblock %}
```

### Requirements & caveats

- **Symfony UX Turbo is required.** Without Turbo on the page, the frame never fetches its `src` and the
  placeholder stays visible. Leave `async` disabled if you don't use Turbo.
- **The table must be rendered on a `GET` URL.** Turbo fetches the deferred frame with a `GET` request to
  the same URL, so async is not suitable on pages reached by a `POST`.
- **Pair it with `DataTableTurboResponseTrait`** (see above) so the deferred fetch returns only the table
  HTML instead of re-rendering the whole page.
- **HTTP caches must `Vary` on the `Turbo-Frame` header.** The same URL returns the placeholder on the
  initial load and the full table on the frame fetch, distinguished only by the `Turbo-Frame` request
  header. If you cache these pages (CDN, reverse proxy, Symfony HttpCache), add `Vary: Turbo-Frame` (or
  mark them non-cacheable) so the two responses are not mixed up.

## Prefetching

<TurboPrefetchingSection>

```php
$builder->addRowAction('show', ButtonActionType::class, [
    'attr' => [
        // note that this "false" should be string, not a boolean
        'data-turbo-prefetch' => 'false',
    ],
]);
```

</TurboPrefetchingSection>
