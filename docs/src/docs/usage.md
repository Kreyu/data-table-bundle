# Usage

[[toc]]

## Creating data tables

Data tables are defined using a _type classes_. Those classes implement [DataTableTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/DataTableTypeInterface.php), although, it is recommended to extend from the [AbstractDataTableType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/AbstractDataTableType.php) class:

```php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Define column, filters, actions, exporters, etc...
    }
}
```

<div class="tip custom-block" style="padding-top: 8px;">

Recommended namespace for the column type classes is `App\DataTable\Type\`.

</div>

From here, you can add [columns](components/columns.md), [filters](components/filters.md), [actions](components/actions.md) and [exporters](components/exporters.md).

In most cases, the data tables are created in the controller, using the `createDataTable()` method from the `DataTableFactoryAwareTrait`.

```php
use App\Repository\ProductRepository;
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait; // [!code highlight]
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait; // [!code highlight]

    public function index(ProductRepository $productRepository)
    {
        $queryBuilder = $productRepository->createQueryBuilder('product');

        $dataTable = $this->createDataTable(ProductDataTableType::class, $queryBuilder);
    }
}
```

This method accepts _three_ arguments:

- type — with a fully qualified class name;
- data — in most cases, an instance of Doctrine ORM query builder;
- options — defined by the data table type, used to configure the data table;

In above example, we're passing an instance of Doctrine ORM query builder as data, not results.
This allows the bundle to paginate the results, apply filtration, and more.

## Handling the request

In order to be able to paginate, sort, filter, personalize or export the data table, call the `handleRequest()` method of the data table:

```php
use App\Repository\ProductRepository;
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(ProductRepository $productRepository) // [!code --]
    public function index(Request $request, ProductRepository $productRepository) // [!code ++]
    {
        $queryBuilder = $productRepository->createQueryBuilder('product');

        $dataTable = $this->createDataTable(ProductDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request); // [!code ++]
    }
}
```

This method calls the [request handler](./features/extensibility.md#request-handlers) to handle all the hard work.

## Rendering the data tables

In order to render the data table, create the data table view and pass it to the template:

```php
use App\Repository\ProductRepository;
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(Request $request, ProductRepository $productRepository)
    {
        $queryBuilder = $productRepository->createQueryBuilder('product');

        $dataTable = $this->createDataTable(ProductDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request);
        
        return $this->render('product/index.html.twig', [ // [!code ++]
            'products' => $dataTable->createView(), // [!code ++]
        ]); // [!code ++]
    }
}
```

:::tip Don't forget to call the `createView()` method!
This creates a `DataTableView` object, used to render the data table.
:::

Now, in the template, render the data table using the `data_table` function:

```twig
{# templates/product/index.html.twig #}

<div class="card">
    {{ data_table(products) }}
</div>
```

By default, the data table will look somewhat _ugly_, because we haven't configured the theme yet - see [theming](features/theming.md) documentation section.

## Setting data table title

The data tables can have a title set, that in built-in themes is displayed on the left side of the card header:

```php
namespace App\DataTable\Type;

use App\Entity\Product;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'title' => 'Products',
        ]);
    }
}
```

For more flexibility, you can set the title directly in the Twig, while rendering the data table:

```twig
{# templates/product/index.html.twig #}

<div class="card">
    {{ data_table(products, { title: 'Products' }) }}
</div>
```

## Setting row attributes

Both header and value rows HTML attributes can be provided by the `header_row_attr` and `value_row_attr`.
The `value_row_attr` accepts a callable, that retrieves data of the row:

```php
namespace App\DataTable\Type;

use App\Entity\Product;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'header_row_attr' => [
                'class' => 'bg-primary',
            ],
            'value_row_attr' => function (Product $product): array {
                return [
                    'class' => $product->isOutOfStock() ? 'bg-danger' : '',
                ];            
            }
        ]);
    }
}
```
