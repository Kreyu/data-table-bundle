# Usage

The recommended workflow when working with this bundle is the following:

1. **Build the data table** in a dedicated data table class;
2. **Render the data table** in a template, so the user can navigate through data;

Each of these steps is explained in detail in the next sections. To make examples easier to follow, all of them assume that you're building an application that displays a list of "products".

Users list products using data table. Each product is an instance of the following `Product` class:

```php
// src/Entity/Product.php
namespace App\Entity;

class Product
{
    private int $id;
    private string $name;
}
```

## Building data tables

This bundle provides a "data table builder" object which allows you to describe the data table using a fluent interface.
Later, the builder created the actual data table object used to render and process contents.

## Creating data tables in controllers

If your controller uses the [DataTableControllerTrait](https://github.com/Kreyu/data-table-bundle/blob/main/src/DataTableControllerTrait.php), use the `createDataTableBuilder()` helper:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        $query = $repository->createQueryBuilder('product');

        $dataTable = $this->createDataTableBuilder($query)
            ->addColumn('id', NumberColumnType::class)
            ->addColumn('name', TextColumnType::class)
            ->getDataTable();
            
        // ...
    }
}
```

In this example, you've added two columns to your data table - `id` and `name` - corresponding to the `id` and `name` properties of the `Product` class.
You've also assigned each a [column type](../reference/columns.md#built-in-column-types) (e.g. `NumberType` and `TextType`), represented by its fully qualified class name.

## Creating data table classes

It is always recommended to put as little logic in controllers as possible.
That's why it's better to move complex data tables to dedicated classes instead of defining them in controller actions.
Besides, data tables defined in classes can be reused in multiple actions and services.

Data table classes are the data table types that implement [DataTableTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/DataTableInterface.php).
However, it's better to extend from [AbstractType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/AbstractType.php), which already implements the interface and provides some utilities:

```php
// src/DataTable/Type/ProductDataTableType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('id', NumberColumnType::class)
            ->addColumn('name', TextColumnType::class)
        ;
    }
}
```

!!! Note

    Install the [MakerBundle](https://symfony.com/bundles/SymfonyMakerBundle/current/index.html) in your project to generate data table classes using the `make:data-table` command.

The data table class contains all the directions needed to create the product data table.
In controllers using the [DataTableControllerTrait](https://github.com/Kreyu/data-table-bundle/blob/main/src/DataTableControllerTrait.php), use the `createDataTable()` helper
(otherwise, use the `create()` method of the `kreyu_data_table.factory` service):

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProductDataTableType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        $query = $repository->createQueryBuilder('product');
        
        $dataTable = $this->createDataTable(ProductDataTableType::class, $query);
            
        // ...
    }
}
```

## Rendering data tables

Now that the data table has been created, the next step is to render it:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProductDataTableType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        $query = $repository->createQueryBuilder('product');
        
        $dataTable = $this->createDataTable(ProductDataTableType::class, $query);
            
        return $this->render('product/index.html.twig', [
            'data_table' => $dataTable->createView(),        
        ]);
    }
}
```

Then, use some [data table helper functions](../reference/twig.md#functions) to render the data table contents:

```html
{# templates/product/index.html.twig #}
{{ data_table(data_table) }}
```

That's it! The [data_table() function](../reference/twig.md#data_tabledata_table_view-variables) renders a complete data table.

!!! Note

    The data table system is smart enough to access the value of the private `id` and `name` properties from each product returned by the query via the `getId()` and `getName()` methods on the `Product` class.
    Unless a property is public, it _must_ have a "getter" method so that [Symfony Property Accessor Component](https://symfony.com/doc/current/components/property_access.html) can read its value.
    For a boolean property, you can use an "isser" or "hasser" method (e.g. `isPublished()` or `hasReminder()`) instead of a getter (e.g. `getPublished` or `getReminder()`).

As short as this rendering is, it's not very flexible.
Usually, you'll need more control about how the entire data table or some of its parts look.
For example, thanks to the [Bootstrap 5 integration with data tables](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/bootstrap_5.html.twig), 
generated data tables are compatible with the Bootstrap 5 CSS framework:

```yaml
# config/packages/kreyu_data_table.yaml
kreyu_data_table:
    themes: ['@KreyuDataTable/themes/bootstrap_5.html.twig']
```

The [built-in themes](../reference/theming.md#built-in-themes) include Bootstrap 5 and Tabler. You can also create your own theme. 

## Processing data tables

The recommended way of processing data tables is to use a single action for both rendering the data table and handling
its pagination, filtration and other features. You can use separate actions, but using one action simplifies everything
while keeping the code concise and maintainable.

Processing a data table means to translate user-submitted data back to the data table (e.g. to change current page).
To make this happen, the submitted data from the user must be written into the data table object:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProductDataTableType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        $query = $repository->createQueryBuilder('product');

        $dataTable = $this->createDataTable(ProductDataTableType::class, $query);
        $dataTable->handleRequest($request);

        return $this->render('product/index.html.twig', [
            'data_table' => $dataTable->createView(),        
        ]);
    }
}
```

This controller follows a common pattern for handling data tables and has two possible paths:

1. When initially loading the page in a browser, the data table hasn't been submitted yet.
   So the data table is created and rendered;
2. When the user submits the data table (e.g. changes current page), `handleRequest()` recognizes this and immediately
   writes the submitted data into the data table. This works the same, as if you've manually extracted the submitted data
   and used the data table's `sort`, `paginate`, `filter` and `personalize` methods.

!!! Note

    If you need more control over exactly when and how your data table is modified, 
    you can use each feature dedicated method to handle the submissions:
    
    - `paginate()` to handle pagination - with current page and limit of items per page;
    - `sort()` to handle sorting - with fields and directions to sort the list;
    - `filter()` to handle filtration - with filters and their values and operators;
    - `personalize()` to handle personalization - with columns visibility status and their order;
    
    The `handleRequest()` method handles all of them manually.
    First argument of the method - the request object - is not tied to specific request implementation,
    although only the [HttpFoundation request handler](https://github.com/Kreyu/data-table-bundle/blob/main/src/Request/HttpFoundationRequestHandler.php) is provided out-of-the-box, 
    [creating custom data table request handler](../advanced/creating-custom-request-handler.md) is easy.

## Passing options to data tables

If you [create data tables in classes](#creating-data-table-classes), when building the data table in the controller, you can pass custom options to it as the third optional argument of `createDataTable()`:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProductDataTableType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        $query = $repository->createQueryBuilder('product');

        // use some PHP logic to decide if this column is displayed or not
        $displayIdentifierColumn = ...;

        $dataTable = $this->createDataTable(ProductDataTableType::class, $query, [
            'display_identifier_column' => $displayIdentifierColumn,
        ]);

        // ...
    }
}
```

If you try to use the data table now, you'll see an error message: _The option "display_identifier_column" does not exist._
That's because data tables must declare all the options they accept using the `configureOptions()` method:

```php
// src/DataTable/Type/ProductDataTableType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    // ...

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // ...,
            'display_identifier_column' => false,
        ]);

        // you can also define the allowed types, allowed values and
        // any other feature supported by the OptionsResolver component
        $resolver->setAllowedTypes('display_identifier_column', 'bool');
    }
}
```

Now you can use this new data table option inside the `buildDataTable()` method:

```php
// src/DataTable/Type/ProductDataTableType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        if ($options['display_identifier_column']) {
            $builder->addColumn('id', NumberColumnType::class);
        }
        
        $builder->addColumn('name', TextColumnType::class);
    }
    
    // ...
}
```