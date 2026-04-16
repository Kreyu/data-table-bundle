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

## Asynchronous Data Table Loading

You can enable asynchronous loading for your data tables using the `async` option. This feature relies on [Turbo Frames lazy loading](https://turbo.hotwired.dev/reference/frames#lazy-loaded-frame) and requires [Symfony UX Turbo](https://symfony.com/bundles/ux-turbo/current/index.html) to be installed. It is especially useful for tables with slow data sources or when displaying multiple tables on a single page.

### Enabling Asynchronous Loading

To enable asynchronous loading globally, add the following to your configuration:

```yaml
# config/packages/kreyu_data_table.yaml
kreyu_data_table:
    defaults:
        async: true
```

Or enable it per data table type:

```php
class ProductDataTableType extends AbstractDataTableType
{
    // ...
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'async' => true,
        ]);
    }
}
```

With this option enabled, the data table will not load its content immediately when the page is rendered. Instead, it will trigger a backend request to fetch and display the table data.

### How It Works

- When `async` is enabled, the table's initial HTML does not include its data rows.
- The table content is loaded asynchronously via a Turbo Frame lazy request after the page loads.
- If the data table is not visible in the user's viewport, its content is not loaded until the user scrolls to it. This optimizes performance, especially on pages with multiple or heavy tables.
- If you need the table to load immediately regardless of visibility, keep `async` set to `false` (the default).

### Recommended: Using `DataTableTurboResponseTrait`

When using `async`, the lazy Turbo Frame request loads the full page and extracts only the matching `<turbo-frame>`. For better performance, use the `DataTableTurboResponseTrait` in your controller to return only the table's HTML when the request comes from a Turbo Frame:

```php
if ($dataTable->isRequestFromTurboFrame()) {
    return $this->createDataTableTurboResponse($dataTable);
}
```

See the [Server-side responses for Turbo Frames](#server-side-responses-for-turbo-frames) section above for a full example.

### Limitations

- The async loading mechanism uses a GET request to `app.request.uri`. If the page is served via POST, the POST parameters will not be included in the async request.


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
