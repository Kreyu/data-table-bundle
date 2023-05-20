---
order: d
---

# Rendering the table

The data table is created, therefore, the next step is to render it to the user.

## Creating data table view

First, pass the data table view to the template. 
The data table view is somewhat a read-only representation of a table. 
It is created using the `createView()` method:

```php #18 src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(ProductRepository $repository)
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $repository->createQueryBuilder('product')
        );
        
        return $this->render('product/index.html.twig', [
            'data_table' => $dataTable->createView(),
        ]);
    }
}
```

Now, create the missing template, and render the data table:

{%{
```twig # templates/product/index.html.twig
<div class="card">
    {{ data_table(data_table) }}
</div>
```
}%}

Voilà! :sparkles: The Twig helper function handles all the work and renders the data table.

## Selecting a theme

Unfortunately, the rendered data table looks _**awful**._ This is because the default theme is being used, which contains only the HTML necessary to base a custom themes on. To fix that, create bundle configuration file and specify desired theme:

+++ YAML
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  themes:
    - '@KreyuDataTable/themes/tabler.html.twig'
```
+++ PHP
```php # config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $config->themes([
        '@KreyuDataTable/themes/tabler.html.twig',
    ]);
};
```
+++

The table is now rendered properly, using a [Tabler UI Kit](https://tabler.io/) theme.
For reference, see [built-in themes](../features/theming.md#built-in-themes).

!!!warning 
The bundle **does not** contain the CSS libraries themselves! \
These **must** be installed and configured individually in the project.
!!!

## Binding request to the data table

Now, when trying to sort the data table by the ID column, **nothing happens** - this is because the data table has _no clue_ the sorting occurred! To fix that, return back to the controller, and use the handy `handleRequest()` method:

```php #18 src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(Request $request, ProductRepository $repository)
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $repository->createQueryBuilder('product')
        );
        
        $dataTable->handleRequest($request);
       
        return $this->render('product/index.html.twig', [
            'data_table' => $dataTable->createView(),
        ]);
    }
}
```

Now the data table is fully interactive, by having access to the request object.

Speaking of interactivity, let's let the user [filter the table](../basic-usage/defining-the-filters.md).
