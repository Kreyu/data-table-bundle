# DataTableBundle

Streamlines creation process of the data tables.

## Installation

Run this command to install the bundle:

```shell
composer require kreyu/data-table-bundle
```

## Usage

The recommended workflow when working with this bundle is the following:

1. **Build the data table** in a dedicated data table class;
2. **Render the data table** in a template, so the user can paginate, sort and filter it;

## Building data tables

Most common way to use the bundle, is to create the data tables in the controllers.  
Controllers should use the [DataTableControllerTrait](), to gain access to the helper methods.

### Creating data table classes

It is always recommended to put as little logic in controllers as possible. 
That's why definition of the data table can be delegated to dedicated classes, instead of defining them in controller actions.
Besides, data tables defined in classes can be reused in multiple actions and services.

Data table classes are the [data table types]() that implement [DataTableTypeInterface](). 
However, it's better to extend from [AbstractType](), which already implements the interface and provides some utilities:

```php
// src/DataTable/Type/ProjectType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\NumericFilter;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\StringFilter;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQuery;
use Kreyu\Bundle\DataTableBundle\Column\Mapper\ColumnMapperInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Kreyu\Bundle\DataTableBundle\Filter\Mapper\FilterMapperInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractType;

class ProjectType extends AbstractType
{
    public function __construct(
        private readonly ProjectRepository $repository,
    ) {
    }
    
    public function createQuery(): ProxyQueryInterface
    {
        return new ProxyQuery($this->repository->createQueryBuilder('project'));
    }

    public function configureColumns(ColumnMapperInterface $columns, array $options): void
    {
        $columns
            ->add('name', TextType::class)
            ->add('quantity', NumberType::class)
        ;
    }
    
    public function configureFilters(FilterMapperInterface $filters, array $options): void
    {
        $filters
            ->add('name', StringFilter::class)
            ->add('quantity', NumericFilter::class)
        ;
    }
}
```

In this example, you've defined a data table with Doctrine query as a data source, with two columns and filters - `name` and `quantity` - corresponding to the `name` and `quantity` properties
of the `Project` class. You've also assigned each a [column type]() (e.g. `TextType` and `NumberType`), represented by its fully qualified class name.

To make life easier and quickly generate data table classes, use the following maker command:

```shell
bin/console make:data-table
```

Now, having the data table type class, let's use the [DataTableControllerTrait](), to gain access to the `createDataTable` method:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProjectType;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
// ...

class ProjectController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request): Response
    {
        $dataTable = $this->createDataTable(ProjectType::class);
        $dataTable->handleRequest($request);
        
        // ...
    }
}
```

Notice the `handleRequest` method- it works as a handy tool to apply pagination, sorting and filters to the query. 
It automatically extracts parameters from the request and calls `sort`, `filter` and `paginate` methods. 
If you're using data tables in place without request (e.g. console command), use those methods manually.

## Rendering data tables

Now that the data table has been created, the next step is to render it:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProjectType;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
// ...

class ProjectController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(): Response
    {
        $dataTable = $this->createDataTable(ProjectType::class);
        $dataTable->handleRequest($request);
        
        return $this->render('project/index.html.twig', [
            'data_table' => $dataTable->createView(),        
        ]);
    }
}
```

Notice the `createView` method- it creates a read-only view object of a data table, that can be easily displayed in the template.

Inside the template, use [data table helper functions]() to render the data table contents:

```html
{# templates/project/index.html.twig #}
{{ data_table(data_table) }}
```

That's it. The [data_table() function]() renders all the data table filters, columns and pagination.

## Other common data table features

### Passing options to data tables

If you [create data tables in classes](), when building the data table in the controller, you can pass custom options to it as the second optional argument of `createDataTable()`:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProjectType;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
// ...

class ProjectController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request): Response
    {
        $dataTable = $this->createDataTable(ProjectType::class, [
            'display_quantity_column' => true,
        ]);
        
        // ...
    }
}
```

If you try to use the data table now, you'll see an error message: _The option "display_quantity_column" does not exist._ That's because forms must declare all the options they accept using the `configureOptions()` method:

```php
// src/DataTable/Type/ProjectType.php
namespace App\DataTable\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    // ...

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // ...
            'display_quantity_column' => false,
        ]);

        // you can also define the allowed types, allowed values and
        // any other feature supported by the OptionsResolver component
        $resolver->setAllowedTypes('display_quantity_column', 'bool');
    }
}
```

Now you can use this new data table option inside the data table class methods:

```php
// src/DataTable/Type/ProjectType.php
namespace App\DataTable\Type;

// ...

class ProjectType extends AbstractType
{
    public function configureColumns(ColumnMapperInterface $columns, array $options): void
    {
        // ...
        
        if ($options['display_quantity_column']) {
            $columns->add('quantity', NumberType::class);            
        }
    }
}
```

## Column types

[//]: # (Add this section)

### Available column types

* [ActionsType](src/Column/Type/ActionsType.php) - to display row actions,
* [BooleanType](src/Column/Type/BooleanType.php) - to display boolean badge with "yes" or "no",
* [CollectionType](src/Column/Type/CollectionType.php) - to display collection of data, e.g. product categories,
* [LinkType](src/Column/Type/LinkType.php) - to display link of given url,
* [NumberType](src/Column/Type/NumberType.php) - to display text formatted as a number (aligned to right),
* [TemplateType](src/Column/Type/TemplateType.php) - to display given twig template,
* [TextType](src/Column/Type/TextType.php) - to display text.

## Filters

### Operators

Because every filter can work differently, e.g. string filter can match exact string or just contain it, each filter supports a set of operators.

Supported operators are defined in the protected `getSupportedOperators()` method of the filter class.

By default, operator selector is not visible to the user. Because of that, first operator choice is always used. If you wish to override that, you can pass selector choices manually:

```php
public function configureFilters(FilterMapperInterface $filters, array $options): void
{
    $filters
        // StringFilter uses Operator::EQUAL by default
        ->add('name', StringFilter::class, [
            'field_name' => 'product.name',
            'operator_options' => [
                'choices' => [
                    Operator::CONTAINS,
                ],
            ],
        ])
    ;
```

If you just want to display operator selector, pass the `operator_options.visible` option to the filter:

```php
public function configureFilters(FilterMapperInterface $filters, array $options): void
{
    $filters
        ->add('quantity', NumericFilter::class, [
            'field_name' => 'product.quantity',
            'operator_options' => [
                'visible' => true,
            ],
        ])
    ;
```

If you wish to override the operator selector completely, create custom form type and pass it as `operator_type` option. Options passed as `operator_options` are used in that type.

### Available filter types

* [CallbackFilter](src/Bridge/Doctrine/Orm/Filter/CallbackFilter.php) - to filter by criteria manually applied to the query in a callback,
* [EntityFilter](src/Bridge/Doctrine/Orm/Filter/EntityFilter.php) - to filter by an entity; displays entity select field,
* [NumericFilter](src/Bridge/Doctrine/Orm/Filter/NumericFilter.php) - to filter by number (supports gt, gte, lt, lte operators)
* [StringFilter](src/Bridge/Doctrine/Orm/Filter/StringFilter.php) - to filter by string
