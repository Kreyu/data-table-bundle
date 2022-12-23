# DataTableBundle

Streamlines creation process of the data tables.

## Installation

In applications using [Symfony Flex](), run this command to install the data table feature before using it:

```
$ composer require kreyu/data-table-bundle
```

## Usage

The recommended workflow when working with this bundle is the following:

1. **Build the data table** in a dedicated data table class;
2. **Render the data table** in a template, so the user can paginate, sort and filter it;

### Data table types

Before creating your first data table, it's important to understand the concept of "data table type".  
Type classes work as a "blueprint", that defines table columns, filters, and its source, e.g. a Doctrine query.

## Building data tables

### Creating data tables in controllers

[//]: # (TODO: Add this section)

### Creating data table classes

Data table classes are the [data table types]() that implement [DataTableTypeInterface]().  
However, it's better to extend from [AbstractType](), which already implements the interface and provides some utilities:

```php
// src/DataTable/Type/ProjectType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Column\Mapper\ColumnMapperInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Kreyu\Bundle\DataTableBundle\Type\AbstractType;

class ProjectType extends AbstractType
{
    public function configureColumns(ColumnMapperInterface $columns): void
    {
        $columns
            ->add('name', TextType::class)
            ->add('quantity', NumberType::class)
        ;
    }
}
```

[//]: # (TODO: Add maker command to create data table types)

The data table class contains all the directions needed to create the data table.  
In the controller, use the [DataTableControllerTrait](), to gain access to the `createDataTable()` helper:

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

**Important**: Notice the `handleRequest` method- it works as a handy tool to apply pagination, sorting and filters to the query.  
It automatically extracts parameters from the request and calls `sort`, `filter` and `paginate` methods.  
If you're using data tables in place without request (e.g. console command), use above methods manually.

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

Then, use some data [table helper functions]() to render the data table contents:

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

[//]: # (Add this section)

### Available filter types

* [CallbackFilter](src/Bridge/Doctrine/Orm/Filter/CallbackFilter.php) - to filter by criteria manually applied to the query in a callback,
* [EntityFilter](src/Bridge/Doctrine/Orm/Filter/EntityFilter.php) - to filter by an entity; displays entity select field,
* [NumericFilter](src/Bridge/Doctrine/Orm/Filter/NumericFilter.php) - to filter by number (supports gt, gte, lt, lte operators)
* [StringFilter](src/Bridge/Doctrine/Orm/Filter/StringFilter.php) - to filter by string
