---
order: d
---

# Rendering the table

The data table is created, therefore the next step is to render it to the user.

## Creating data table view

First, pass the data table view to the template. The data table view is somewhat a read-only representation of a table, created by the `createView(`) method:

<pre class="language-php" data-title="src/Controller/ProductController.php" data-line-numbers><code class="lang-php">use App\DataTable\Type\ProductDataTableType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(ProductRepository $repository)
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $repository->createQueryBuilder('product')
        );
        
        return $this->render('product/index.html.twig', [
<strong>            'data_table' => $dataTable->createView(),
</strong>        ]);
    }
}
</code></pre>

Now, create the missing template, and render the data table:

{% code title="templates/product/index.html.twig" lineNumbers="true" %}
```twig
<div class="card">
    {{ data_table(data_table) }}
</div>
```
{% endcode %}

Voilà! :sparkles: The Twig helper function handles all the work and renders the data table.

## Selecting a theme

Unfortunately, the rendered data table looks _**awful**._ This is because the default theme is being used, which contains only the HTML necessary to base a custom themes on. To fix that, create bundle configuration file and specify desired theme:

{% tabs %}
{% tab title="YAML" %}
{% code title="config/packages/kreyu_data_table.yaml" lineNumbers="true" %}
```yaml
kreyu_data_table:
  themes:
    - '@KreyuDataTable/themes/tabler.html.twig'
```
{% endcode %}
{% endtab %}

{% tab title="PHP" %}
{% code title="config/packages/kreyu_data_table.php" lineNumbers="true" %}
```php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->themes([
        '@KreyuDataTable/themes/tabler.html.twig',
    ]);
};
```
{% endcode %}
{% endtab %}
{% endtabs %}

The table is now rendered properly, using a [Tabler UI Kit](https://tabler.io/) theme.&#x20;

For reference, see [built-in themes](../reference/theming.md#built-in-themes).

{% hint style="warning" %}
**Warning**

The bundle **does not** contain the CSS libraries themselves! \
These **must** be installed and configured individually in the project.
{% endhint %}

{% hint style="info" %}
**Note**

Following articles contain screenshots (and HTML classes in some code examples) with this theme in mind. This theme is based on Bootstrap 5, therefore the differences between them are minimal.
{% endhint %}

## Binding request to the data table

Now, when trying to sort the data table by the ID column, **nothing happens** - this is because the data table has _no clue_ the sorting occurred! To fix that, return back to the controller, and use the handy `handleRequest()` method:

<pre class="language-php" data-title="src/Controller/ProductController.php" data-line-numbers><code class="lang-php">use App\DataTable\Type\ProductDataTableType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $repository)
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $repository->createQueryBuilder('product')
        );
        
<strong>        $dataTable->handleRequest($request);
</strong>        
        return $this->render('product/index.html.twig', [
            'data_table' => $dataTable->createView(),
        ]);
    }
}
</code></pre>

Now the data table is fully interactive, by having access to the request object.

Speaking of interactivity, let's let the user [filter the table](../basic-usage/defining-the-filters.md).
