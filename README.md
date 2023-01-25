# DataTableBundle

[//]: # ([![Latest Stable Version]&#40;http://poser.pugx.org/kreyu/data-table-bundle/v&#41;]&#40;https://packagist.org/packages/kreyu/data-table-bundle&#41;)
[//]: # ([![PHP Version Require]&#40;http://poser.pugx.org/kreyu/data-table-bundle/require/php&#41;]&#40;https://packagist.org/packages/kreyu/data-table-bundle&#41;)

Streamlines creation process of the data tables in Symfony 6.  
Heavily inspired by the [Symfony Form](https://github.com/symfony/form) & [Sonata Admin Bundle](https://github.com/sonata-project/SonataAdminBundle) datagrid.

Features:

- class-based data table definition to reduce repeated codebase;
- source data pagination, filtration and sorting (supports persistence);
- personalization (supports persistence), where user is able to:
  - show/hide desired columns;
  - change order of the columns;
- exporting, where user is able to:
  - select desired output format;
  - decide whether the records should be included only from currently displayed page, or from the whole table;
  - decide whether the personalization should be included;
- Doctrine ORM support by default, but open to custom implementation;

## Table of contents

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
- [Filtration](#filtration)
  - Persistence
    - Configuring default cache persister
    - Creating a custom filtration persister
    - Differentiating persistence data per subject
    - Retrieving persistence data subjects
    - Passing the persistence subject directly
  - [Available filters](#available-filters)
  - [Creating custom filter](#creating-custom-filter)
    - [Doctrine ORM](#doctrine-orm)
  - [Filter operators](#filter-operators)

#### Persistence

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


## Persistence

This bundle provides persistence feature, ready to use with data table sorting, pagination, filtration and personalization.

### Persistence adapters

Adapters are classes that allows writing (to) and reading (from) the persistent data source.  
By default, there's only one adapter integrating with Symfony Cache contracts.

#### Using built-in cache adapter

Built-in cache adapter accepts two arguments in constructor:

- cache implementing Symfony's `Symfony\Contracts\Cache\CacheInterface`
- prefix string used to differentiate different data sets, e.g. filtration persistence uses `filtration` prefix

In service container, it is registered as an [abstract service](https://symfony.com/doc/current/service_container/parent_services.html):

```shell
bin/console debug:container kreyu_data_table.persistence.adapter.cache
```

Creating new services based on the abstract adapter can be performed in service container.

#### Creating custom adapters

To create a custom adapter, create a class that implements `PersistenceAdapterInterface`:

```php
// src/DataTable/Persistence/DatabasePersistenceAdapter.php
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;

class DatabasePersistenceAdapter implements PersistenceAdapterInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private string $prefix,
    ) {
    }
    
    public function read(DataTableInterface $dataTable, PersistenceSubjectInterface $subject): mixed
    {
        // ...
    }

    public function write(DataTableInterface $dataTable, PersistenceSubjectInterface $subject, mixed $data): void
    {
        // ...
    }
}
```

... and register it in the container as an abstract service:

```yaml
services:
  app.data_table.persistence.database:
      class: App\DataTable\Persistence\DatabasePersistenceAdapter
      abstract: true
      arguments:
        - '@doctrine.orm.entity_manager'
```



### Persistence subjects

Persistence subject can be any object that implements `PersistenceSubjectInterface`.

In most cases, the persistence subject will be a User entity, so don't forget to implement the required interface:

```php
// src/Entity/User.php
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;

class User implements PersistenceSubjectInterface
{
    private ?int $id = null;
    
    public function getDataTablePersistenceIdentifier(): string
    {
        return (string) $this->id;
    }
}
```

The value returned in the `getDataTablePersistenceIdentifier()` is used in [persistence adapters](#adapters)
to associate persistent data with the subject.

### Persistence subject providers

Persistence subject providers are classes that allows retrieving the [persistence subjects](#persistence-subjects).  
Those classes contain `provide` method, that should return the subject, or throw an `PersistenceSubjectNotFoundException`.  
By default, there's only one provider, integrating with Symfony token storage, to retrieve currently logged-in user.

#### Creating custom persistence subject providers

To create a custom subject provider, create a class that implements `PersistenceSubjectProviderInterface`:

```php
// src/DataTable/Persistence/StaticPersistenceSubjectProvider.php
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;

class StaticPersistenceSubjectProvider implements PersistenceSubjectProviderInterface
{
    public function provide(): PersistenceSubjectInterface
    {
        return new class implements PersistenceSubjectInterface
        {
            public function getDataTablePersistenceIdentifier(): string
            {
                return 'static';
            }
        }
    }
}
```

When using default container configuration, that provider should be ready to use.  
If not, consider tagging this class as `kreyu_data_table.persistence.subject_provider`:

```yaml
services:
  app.data_table.persistence.subject_provider.static:
    class: App\DataTable\Persistence\StaticPersistenceSubjectProvider
    tags:
      - { name: kreyu_data_table.persistence.subject_provider }
```

## Filtration

Source data can be filtered by the criteria given by the user.

This feature can be disabled by overriding the `isFiltrationEnabled()` method of the data table type class:

```php
// src/DataTable/Type/ProjectType.php
class ProjectType extends AbstractType
{
    public function isFiltrationEnabled(): bool
    {
        return false;
    }
}
```

### Filtration criteria persistence

By default, filtration criteria applied by the user is saved to the cache for later use.

This feature can be disabled by overriding the `isFiltrationPersistenceEnabled()` method of the data table type class:

```php
// src/DataTable/Type/ProjectType.php
class ProjectType extends AbstractType
{
    public function isFiltrationPersistenceEnabled(): bool
    {
        return false;
    }
}
```

#### Configuring the filtration persistence adapter

To read about the persistence adapters, see [persistence adapters](#persistence-adapters) section.

For filtration, by default, there's a cache adapter service already pre-configured:

```shell
bin/console debug:container kreyu_data_table.filtration.persistence.adapter.cache
```

Recommended way to change the filtration persistence adapter used by the data table type class, is to use setter injection:

```yaml
# config/services.yaml
services:
  App\DataTable\Type\ProjectType:
    calls:
      - setFiltrationPersistenceAdapter: ['@kreyu_data_table.filtration.persistence.adapter.cache']
```

#### Passing the persistence subject directly

In some cases, it may be more handy to provide a persistence subject directly, instead of using a provider.
To do so, override the `getFiltrationPersistenceSubject()` method of the data table type class:

```php
// src/DataTable/Type/ProjectType.php
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;

class ProjectType extends AbstractType
{
    public function getFiltrationPersistenceSubject(): PersistenceSubjectInterface
    {
        // return the subject directly
    }
}
```

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
# config/services.yaml
services:
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
