# DataTableBundle

[![Latest Stable Version](http://poser.pugx.org/kreyu/data-table-bundle/v)](https://packagist.org/packages/kreyu/data-table-bundle)

Streamlines creation process of the data tables in Symfony applications.

> ### ️ℹ️ Note
> This bundle structure was heavily inspired by the [Symfony Form](https://github.com/symfony/form) component.

### Features

- class-based definition of data tables to reduce repeated codebase;
- source data pagination, filtration and sorting;
- filters supporting multiple operators (e.g. user can select if string filter contains or equals given value);
- per-user persistence with [cache component](https://symfony.com/doc/current/components/cache.html) by default;
- extension system used to modify data tables across the entire application;
- personalization, where user is able to show/hide or even change order of the columns;
- exporting, where user is able to export data tables to various file formats;
- logic decoupled from the source of the data;
- easy theming of every part of the bundle;


## Table of contents

* [Installation](#installation)
  * [Integration with Symfony UX](#integration-with-symfony-ux)
* [Usage](#usage)
  * [Building data tables](#building-data-tables)
  * [Creating data tables in controllers](#creating-data-tables-in-controllers)
  * [Creating data table classes](#creating-data-table-classes)
  * [Rendering data tables](#rendering-data-tables)
  * [Processing data tables](#processing-data-tables)
  * [Passing options to data tables](#passing-options-to-data-tables)
  * [Creating data table type extension](#creating-data-table-type-extension)
* [Columns](#columns)
  * [Available column types](#available-column-types)
  * [Creating custom column type](#creating-custom-column-type)
  * [Creating column type extension](#creating-column-type-extension)
* [Filtration](#filtration)
  * [Configuring the filtration feature](#configuring-the-filtration-feature)
  * [Available filter types](#available-filter-types)
  * [Using filter operators](#using-filter-operators)
* [Sorting](#sorting)
  * [Configuring the sorting feature](#configuring-the-sorting-feature)
* [Pagination](#pagination)
  * [Configuring the pagination feature](#configuring-the-pagination-feature)
* [Personalization](#personalization)
  * [Configuring the personalization feature](#configuring-the-personalization-feature)
* [Exporting](#exporting)
  * [Configuring the exporting feature](#configuring-the-exporting-feature)
  * [Available exporter types](#available-exporter-types)
  * [Creating custom exporter type](#creating-custom-exporter-type)
* [Persistence](#persistence)
  * [Persistence adapters](#persistence-adapters)
    * [Using built-in cache adapter](#using-built-in-cache-adapter)
    * [Creating custom adapters](#creating-custom-adapters)
  * [Persistence subjects](#persistence-subjects)
  * [Persistence subject providers](#persistence-subject-providers)
    * [Creating custom persistence subject providers](#creating-custom-persistence-subject-providers)
* [Learn more](#learn-more)

## Installation

Run this command to install the bundle:

```shell
composer require kreyu/data-table-bundle
```

If you wish to use the [Doctrine ORM](https://github.com/doctrine/orm), install the [DataTableDoctrineOrmBundle](https://github.com/Kreyu/data-table-doctrine-orm-bundle):

```shell
composer require kreyu/data-table-doctrine-orm-bundle
```

If you wish to use the exporting feature with [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet), install the [DataTablePhpSpreadsheetBundle](https://github.com/Kreyu/data-table-phpspreadsheet-bundle):

```shell
composer require kreyu/data-table-phpspreadsheet-bundle
```

### Integration with Symfony UX

This bundle provides front-end scripts created using the [Stimulus JavaScript framework](https://stimulus.hotwired.dev/).
To begin with, make sure your application uses the [Symfony Stimulus Bridge](https://github.com/symfony/stimulus-bridge).

Because the bundle is tagged as the `symfony-ux`, the [Symfony Flex](https://github.com/symfony/flex) 
should automatically configure the front-end controllers for you.

To confirm that, first check your `package.json`, that should contain a `@kreyu/data-table-bundle` dependency:

```json5
{
    "devDependencies": {
        // ...
        "@kreyu/data-table-bundle": "file:vendor/kreyu/data-table-bundle/assets",
    }
}
```

Then, check your [assets/controllers.json](https://github.com/symfony/stimulus-bridge#the-controllersjson-file) file, which should contain following configuration:

```json5
{
    "controllers": {
        // ...
        "@kreyu/data-table-bundle": {
            "personalization": {
                "enabled": true,
                "fetch": "eager"
            }
        }
    },
    // ...
}
```

Last but not least, remember to run install & build front-end dependencies:

```shell
# if using npm
npm install
npm run watch

# if using yarn
yarn
yarn watch
```


## Usage

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

### Building data tables

This bundle provides a "data table builder" object which allows you to describe the data table using a fluent interface.
Later, the builder created the actual data table object used to render and process contents.

### Creating data tables in controllers

If your controller uses the [DataTableControllerTrait](src/DataTableControllerTrait.php), use the `createDataTableBuilder()` helper:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableDoctrineOrmBundle\Query\ProxyQuery;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        $products = $repository->createQueryBuilder('product');

        $dataTable = $this->createDataTableBuilder(new ProxyQuery($products))
            ->addColumn('id', NumberType::class)
            ->addColumn('name', TextType::class)
            ->getDataTable();
            
        // ...
    }
}
```

In this example, you've added two columns to your data table - `id` and `name` - corresponding to the `id` and `name` properties of the `Product` class.
You've also assigned each a [column type](#available-column-types) (e.g. `NumberType` and `TextType`), represented by its fully qualified class name.

> ### 💡 Important note
> Notice the use of the `ProxyQuery` class, which wraps the query builder.
> Classes implementing the `ProxyQueryInterface` are used to modify the underlying query by the data tables.
> 
> In this example, the [Doctrine ORM](https://github.com/doctrine/orm) is used, and the proxy class comes from the `kreyu/data-table-doctrine-orm-bundle` package.
> 
> For custom implementation, see [creating custom proxy query classes](docs/create_custom_proxy_query_classes.md).

### Creating data table classes

It is always recommended to put as little logic in controllers as possible.
That's why it's better to move complex data tables to dedicated classes instead of defining them in controller actions.
Besides, data tables defined in classes can be reused in multiple actions and services.

Data table classes are the data table types that implement [DataTableTypeInterface](src/DataTableInterface.php).
However, it's better to extend from [AbstractType](src/Type/AbstractType.php), which already implements the interface and provides some utilities:

```php
// src/DataTable/Type/ProductType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\NumberType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractType;

class ProductType extends AbstractType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('id', NumberType::class)
            ->addColumn('name', TextType::class)
        ;
    }
}
```

> ### 💡 Important note
> Install the [MakerBundle](https://symfony.com/bundles/SymfonyMakerBundle/current/index.html) in your project to generate data table classes using the `make:data-table` command.

The data table class contains all the directions needed to create the product data table.
In controllers using the [DataTableControllerTrait](src/DataTableControllerTrait.php), use the `createDataTable()` helper
(otherwise, use the `create()` method of the `kreyu_data_table.factory` service):

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProductType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableDoctrineOrmBundle\Query\ProxyQuery;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        $products = $repository->createQueryBuilder('product');
        
        $dataTable = $this->createDataTable(ProductType::class, new ProxyQuery($products));
            
        // ...
    }
}
```

### Rendering data tables

Now that the data table has been created, the next step is to render it:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProductType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableDoctrineOrmBundle\Query\ProxyQuery;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        $products = $repository->createQueryBuilder('product');
        
        $dataTable = $this->createDataTable(ProductType::class, new ProxyQuery($products));
            
        return $this->render('product/index.html.twig', [
            'data_table' => $dataTable->createView(),        
        ]);
    }
}
```

Then, use some [data table helper functions](docs/twig_reference.md#functions) to render the data table contents:

```html
{# templates/product/index.html.twig #}
{{ data_table(data_table) }}
```

That's it! The [data_table() function](docs/twig_reference.md#datatabledatatableview-variables) renders a complete data table.

> ### 💡 Important note
>
> The data table system is smart enough to access the value of the private `id` and `name` properties from each product returned by the query via the `getId()` and `getName()` methods on the `Product` class.
> Unless a property is public, it _must_ have a "getter" method so that [Symfony Property Accessor Component](https://symfony.com/doc/current/components/property_access.html) can read its value.
> For a boolean property, you can use an "isser" or "hasser" method (e.g. `isPublished()` or `hasReminder()`) instead of a getter (e.g. `getPublished` or `getReminder()`).

As short as this rendering is, it's not very flexible.
Usually, you'll need more control about how the entire data table or some of its parts look.
For example, thanks to the [Bootstrap 5 integration with data tables](src/Resources/views/themes/bootstrap_5.html.twig), generated data tables are compatible with the Bootstrap 5 CSS framework:

```yaml
# config/packages/kreyu_data_table.yaml
kreyu_data_table:
    themes: 
      - '@KreyuDataTable/themes/bootstrap_5.html.twig' # default value
```

### Processing data tables

The recommended way of processing data tables is to use a single action for both rendering the data table and handling
its pagination, filtration and other features. You can use separate actions, but using one action simplifies everything
while keeping the code concise and maintainable.

Processing a data table means to translate user-submitted data back to the data table (e.g. to change current page).
To make this happen, the submitted data from the user must be written into the data table object:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProductType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableDoctrineOrmBundle\Query\ProxyQuery;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        $products = $repository->createQueryBuilder('product');

        $dataTable = $this->createDataTable(ProductType::class, new ProxyQuery($products));
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

> ### 💡 Important note
> If you need more control over exactly when and how your data table is modified, you can use each feature dedicated method to handle the submissions:
>
> - `paginate()` to handle pagination - with current page and limit of items per page;
> - `sort()` to handle sorting - with fields and directions to sort the list. Supports sorting by multiple fields;
> - `filter()` to handle filtration - with filters and their values and operators;
> - `personalize()` to handle personalization - with columns visibility status and their order;
>
> The `handleRequest()` method handles all of them manually.
> First argument of the method - the request object - is not tied to specific request implementation,
> although only the [HttpFoundation request handler](src/Request/HttpFoundationRequestHandler.php) is provided out-of-the-box, [creating custom data table request handler](docs/create_custom_request_handler.md) is easy.

### Passing options to data tables

If you [create data tables in classes](#creating-data-table-classes), when building the data table in the controller, you can pass custom options to it as the third optional argument of `createDataTable()`:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProductType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableDoctrineOrmBundle\Query\ProxyQuery;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        $products = $repository->createQueryBuilder('product');

        // use some PHP logic to decide if this column is displayed or not
        $displayIdentifierColumn = ...;

        $dataTable = $this->createDataTable(ProductType::class, new ProxyQuery($products), [
            'display_identifier_column' => $displayIdentifierColumn,
        ]);

        // ...
    }
}
```

If you try to use the data table now, you'll see an error message: _The option "display_identifier_column" does not exist._
That's because data tables must declare all the options they accept using the `configureOptions()` method:

```php
// src/DataTable/Type/ProductType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
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
// src/DataTable/Type/ProductType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\NumberType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        if ($options['display_identifier_column']) {
            $builder->addColumn('id', NumberType::class);
        }
        
        $builder->addColumn('name', TextType::class);
    }
    
    // ...
}
```

### Creating data table type extension

See [How to Create a Data Table Type Extension]().

## Columns

A data table is composed of _columns_, each of which are built with the help of a column _type_ (e.g. `NumberType`, `TextType`, etc).

### Available column types

The following column types are natively available in the bundle:

- Text types
    - [TextType](docs/column/types/text.md)
    - [NumberType](docs/column/types/number.md)
    - [BooleanType](docs/column/types/boolean.md)
    - [LinkType](docs/column/types/link.md)
- Special types
    - [CollectionType](docs/column/types/collection.md)
    - [TemplateType](docs/column/types/template.md)
    - [ActionsType](docs/column/types/actions.md)
- Base types
    - [ColumnType](docs/column/types/column.md)

### Creating custom column type

See [How to Create a Custom Column Type](docs/column/create_custom_column_type.md).

### Creating column type extension

See [How to Create a Column Type Extension](docs/column/create_column_type_extension.md).

## Filtration

A data table can be filtered with a set of _filters_, each of which are built with the help of a filter _type_ (e.g. `StringType`, `EntityType`, etc),

### Configuring the filtration feature

By default, the filtration is enabled for every data table type.

Every part of the feature can be configured using the [data table options](#passing-options-to-data-tables):

- `filtration_enabled` - to enable/disable feature completely;
- `filtration_persistence_enabled` - to enable/disable feature [persistence](#persistence);
- `filtration_persistence_adapter` - to change the [persistence adapter](#persistence-adapters);
- `filtration_persistence_subject` - to change the [persistence subject](#persistence-subjects) directly;

By default, if the feature is enabled, the [persistence adapter](#persistence-adapters) and [subject provider](#persistence-subject-providers) are autoconfigured.

### Available filter types

Filter application methods differ between various data providers.
Therefore, only the [base filter type](docs/filter/types/filter.md) is available natively in the bundle.

If your application uses the [Doctrine ORM](https://github.com/doctrine/orm), you can use the [kreyu/data-table-doctrine-orm-bundle](https://github.com/Kreyu/data-table-doctrine-orm-bundle), 
which provides several Doctrine ORM oriented [filter types](https://github.com/Kreyu/data-table-doctrine-orm-bundle#available-filter-types).

### Using filter operators

Let's assume, that the product data table contains two products, named:

- Product A
- Product B

There are multiple ways of handling that filtration, for example:

- matching exact string, e.g. "Product" will not find any matches,
- matching only beginning of a string, e.g. "Product" will match both "Product A" and "Product B",

To support such cases, each filter can support a set of operators.

By default, the operator selector is not visible to the user. Because of that, first operator choice is always used. 

To display the operator selector, pass the `operator_options.visible` option to the filter:

```php
// src/DataTable/Type/ProductType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractType;

class ProductType extends AbstractType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // ...

        $builder
            ->addFilter('name', StringType::class, [
                'query_path' => 'product.name',
                'operator_options' => [
                    'visible' => true,
                ],
            ])
        ;
    }
}
```

If you wish to restrain operators available to select, pass the `operator_options.choices` option to the filter:

```php
// src/DataTable/Type/ProductType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractType;

class ProductType extends AbstractType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // ...

        $builder
            ->addFilter('name', StringType::class, [
                'query_path' => 'product.name',
                'operator_options' => [
                    'visible' => true,
                    'choices' => [
                        Operator::EQUALS,
                        Operator::STARTS_WITH,
                    ],
                ],
            ])
        ;
    }
}
```

If you wish to override the operator selector completely, create custom form type and pass it as `operator_type` option.
Options passed as `operator_options` are used in that type.


## Sorting

This bundle provides sorting feature, what gives users the ability to sort the data table by its columns.

### Configuring the sorting feature

By default, the sorting is enabled for every data table type.

Every part of the feature can be configured using the [data table options](#passing-options-to-data-tables):

- `sorting_enabled` - to enable/disable feature completely;
- `sorting_persistence_enabled` - to enable/disable feature persistence;
- `sorting_persistence_adapter` - to change the persistence adapter;
- `sorting_persistence_subject` - to change the persistence subject directly;

By default, if the feature is enabled, the [persistence adapter](#persistence-adapters) and [subject provider](#persistence-subject-providers) are autoconfigured.


## Pagination

This bundle provides pagination feature, what gives users the ability to display data in chunks, which saves memory on huge data sources.

### Configuring the pagination feature

By default, the pagination is enabled for every data table type.

Every part of the feature can be configured using the [data table options](#passing-options-to-data-tables):

- `pagination_enabled` - to enable/disable feature completely;
- `pagination_persistence_enabled` - to enable/disable feature persistence;
- `pagination_persistence_adapter` - to change the persistence adapter;
- `pagination_persistence_subject` - to change the persistence subject directly;

By default, if the feature is enabled, the [persistence adapter](#persistence-adapters) and [subject provider](#persistence-subject-providers) are autoconfigured.


## Personalization

This bundle provides personalization feature, what gives users the ability to freely show/hide specific columns and even set their order per data-table basis.

### Configuring the personalization feature

By default, the personalization is enabled for every data table type.

Every part of the feature can be configured using the [data table options](#passing-options-to-data-tables):

- `personalization_enabled` - to enable/disable feature completely;
- `personalization_persistence_enabled` - to enable/disable feature persistence;
- `personalization_persistence_adapter` - to change the persistence adapter;
- `personalization_persistence_subject` - to change the persistence subject directly;

By default, if the feature is enabled, the [persistence adapter](#persistence-adapters) and [subject provider](#persistence-subject-providers) are autoconfigured.


## Exporting

A data table can be exporter to various formats, using _exporters_, each of which are built with the help of a exporter _type_ (e.g. `CsvType`, `XlsxType`, etc),

### Configuring the exporting feature

By default, the exporting is enabled for every data table type.

Every part of the exporting feature can be configured using the [data table options](#passing-options-to-data-tables):

- `exporting_enabled` - to enable/disable feature completely;

### Available exporter types

Exporters in general depend on external libraries.
This bundle does not force the usage of any specific implementation, therefore, only the [base exporter type](docs/exporter/types/exporter.md) is available natively in the bundle.

If your application uses the [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet), you can use the [kreyu/data-table-phpspreadsheet-bundle](https://github.com/Kreyu/data-table-phpspreadsheet-bundle),
which provides several PhpSpreadsheet oriented [exporter types](https://github.com/Kreyu/data-table-phpspreadsheet-bundle#available-exporter-types).

### Creating custom exporter type

See [How to Create a Custom Exporter Type]().


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

...and register it in the container as an abstract service:

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

The value returned in the `getDataTablePersistenceIdentifier()` is used in [persistence adapters](#persistence-adapters)
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

## Learn more

- [Create Custom Request Handler](docs/create_custom_request_handler.md)
- [Create Custom Proxy Query Classes](docs/create_custom_proxy_query_classes.md)
- [Twig reference](docs/twig_reference.md)

## License

The MIT License (MIT). Please see [license file](LICENSE) for more information.
