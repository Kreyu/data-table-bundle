# DataTableBundle

[//]: # ([![Latest Stable Version]&#40;http://poser.pugx.org/kreyu/data-table-bundle/v&#41;]&#40;https://packagist.org/packages/kreyu/data-table-bundle&#41;)
[//]: # ([![PHP Version Require]&#40;http://poser.pugx.org/kreyu/data-table-bundle/require/php&#41;]&#40;https://packagist.org/packages/kreyu/data-table-bundle&#41;)

Streamlines creation process of the data tables in Symfony 6.  
Heavily inspired by the [Symfony Form](https://github.com/symfony/form) & [Sonata Admin Bundle](https://github.com/sonata-project/SonataAdminBundle) datagrid.

- [Installation](#installation)
- [Usage](#usage)
- [Building data tables](#building-data-tables)
  - [Creating data table classes](#creating-data-table-classes)
- [Rendering data tables](#rendering-data-tables)
- [Other common data table features](#other-common-data-table-features)
  - [Passing options to data tables](#passing-options-to-data-tables)
  - [Using Twig helper functions](#using-twig-helper-functions)
- [Columns](#columns)
  - [Available column types](#available-column-types)
  - [Creating custom column type](#creating-custom-column-type)
- [Filters](#filters)
  - [Available filters](#available-filters)
  - [Creating custom filter](#creating-custom-filter)
    - [Doctrine ORM](#doctrine-orm)
  - [Filter operators](#filter-operators)

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
Controllers should use the [DataTableControllerTrait](src/DataTableControllerTrait.php), to gain access to the helper methods.

### Creating data table classes

It is always recommended to put as little logic in controllers as possible. 
That's why definition of the data table can be delegated to dedicated classes, instead of defining them in controller actions.
Besides, data tables defined in classes can be reused in multiple actions and services.

Data table classes are the data table types that implement [DataTableTypeInterface](src/DataTableInterface.php). 
However, it's better to extend from [AbstractType](src/Type/AbstractType.php), which already implements the interface and provides some utilities:

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

Now, having the data table type class, let's use the [DataTableControllerTrait](src/DataTableControllerTrait.php), to gain access to the `createDataTable` method:

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

Inside the template, use [data table twig helper functions](#using-twig-helper-functions) to render the data table contents:

```html
{# templates/project/index.html.twig #}
{{ data_table(data_table) }}
```

That's it. The `data_table` function renders all the data table filters, columns and pagination.

## Other common data table features

### Passing options to data tables

If you [create data tables in classes](#creating-data-table-classes), when building the data table in the controller, you can pass custom options to it as the second optional argument of `createDataTable()`:

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

### Using Twig helper functions

Functions are defined in the [DataTableExtension class](src/Bridge/Twig/DataTableExtension.php).

| Function                          | Arguments                                                                                       | Description                                                             |
|:----------------------------------|:------------------------------------------------------------------------------------------------|:------------------------------------------------------------------------|
| `data_table`                      | 1) `DataTableViewInterface $dataTable`                                                          | Renders whole data table, including filter form, columns and pagination |
| `data_table_column_label`         | 1) `DataTableViewInterface $dataTable`<br/> 2) `ColumnInterface $column`                        | Renders column label                                                    |
| `data_table_column_header`        | 1) `DataTableViewInterface $dataTable`<br/> 2) `ColumnInterface $column`                        | Renders column header - same as label, but with sortable behavior       |
| `data_table_column_value`         | 1) `DataTableViewInterface $dataTable`<br/> 2) `ColumnInterface $column`<br/> 3) `mixed $value` | Renders column value. Gets a raw value, outputs a formatted value       |
| `data_table_filter_form`          | 1) `DataTableViewInterface $dataTable`                                                          | Renders filter form                                                     |
| `data_table_personalization_form` | 1) `DataTableViewInterface $dataTable`                                                          | Renders personalization form                                            |
| `data_table_pagination`           | 1) `DataTableViewInterface $dataTable`                                                          | Renders pagination                                                      |

## Columns

### Available column types

The following column types are natively available in the bundle:

- Text types
  - [TextType](docs/column/types/text.md)
  - [NumberType](docs/column/types/number.md)
  - [BooleanType](docs/column/types/boolean.md)
  - [LinkType](docs/column/types/link.md)
- Special docs/column/types
  - [CollectionType](docs/column/types/collection.md)
  - [TemplateType](docs/column/types/template.md)
  - [ActionsType](docs/column/types/actions.md)
- Other
  - [AbstractType](docs/column/types/abstract.md)

### Creating custom column type

To create a custom column type, create a class that extends `Kreyu\Bundle\DataTableBundle\Column\Type\AbstractType`.

When using default container configuration, that type should be ready to use.  
If not, remember to tag this class as `kreyu_data_table.column_type`:

```yaml
App\DataTable\Column\Type\MyCustomType:
  tags:
    - { name: 'kreyu_data_table.column_type' }
```

## Filters

### Available filters

The following filters are natively available in the bundle:

- Doctrine ORM
  - [StringFilter](docs/filter/doctrine/orm/string.md)
  - [NumericFilter](docs/filter/doctrine/orm/numeric.md)
  - [EntityFilter](docs/filter/doctrine/orm/entity.md)
  - [CallbackFilter](docs/filter/doctrine/orm/callback.md)
- Other
  - [AbstractFilter](docs/filter/other/abstract.md)

### Creating custom filter

#### Doctrine ORM

To create a custom filter, create a class that extends `Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\AbstractFilter`.

When using default container configuration, that filter should be ready to use.  
If not, remember to tag this class as `kreyu_data_table.filter`:

```yaml
App\DataTable\Filter\MyCustomFilter:
  tags:
    - { name: 'kreyu_data_table.filter' }
```

### Filter operators

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
}
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
}
```

If you wish to override the operator selector completely, create custom form type and pass it as `operator_type` option. 
Options passed as `operator_options` are used in that type.

## TODO

- [X] Personalization (to let user change the columns visibility and order);
- [X] Filter & personalization persistence (to save filters & personalization applied by the user);
- [ ] Personalization docs
- [ ] Filter & personalization persistence docs
- [ ] Export to excel (both with and without personalization & applied filters);
