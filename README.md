# DataTableBundle

[//]: # ([![Latest Stable Version]&#40;http://poser.pugx.org/kreyu/data-table-bundle/v&#41;]&#40;https://packagist.org/packages/kreyu/data-table-bundle&#41;)
[//]: # ([![PHP Version Require]&#40;http://poser.pugx.org/kreyu/data-table-bundle/require/php&#41;]&#40;https://packagist.org/packages/kreyu/data-table-bundle&#41;)

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
- support for [Doctrine ORM](https://github.com/doctrine/orm) by default, but open to custom implementation;

## Table of contents

* [Installation](#installation)
* [Usage](#usage)
* [Building data tables](#building-data-tables)
    * [Creating data tables in controllers](#creating-data-tables-in-controllers)
    * [Creating data table classes](#creating-data-table-classes)
* [Rendering data tables](#rendering-data-tables)
* [Processing data tables](#processing-data-tables)
* [Other common data table features](#other-common-data-table-features)
    * [Passing options to data tables](#passing-options-to-data-tables)
* [Columns](#columns)
    * [Available column types](#available-column-types)
    * [Creating custom column type](#creating-custom-column-type)
    * [Creating column type extension](#creating-column-type-extension)
* [Filters](#filters)
    * [Available filter types](#available-filter-types)
    * [Creating custom filter type](#creating-custom-filter-type)
    * [Creating filter type extension](#creating-filter-type-extension)
    * [Filter operators](#filter-operators)
* [Persistence](#persistence)
    * [Persistence adapters](#persistence-adapters)
        * [Using built-in cache adapter](#using-built-in-cache-adapter)
        * [Creating custom adapters](#creating-custom-adapters)
    * [Persistence subjects](#persistence-subjects)
    * [Persistence subject providers](#persistence-subject-providers)
        * [Creating custom persistence subject providers](#creating-custom-persistence-subject-providers)
    * [Filtration criteria persistence](#filtration-criteria-persistence)
        * [Configuring the filtration persistence adapter](#configuring-the-filtration-persistence-adapter)
        * [Passing the persistence subject directly](#passing-the-persistence-subject-directly)

## Installation

Run this command to install the bundle:

```shell
composer require kreyu/data-table-bundle
```

## Usage

The recommended workflow when working with this bundle is the following:

1. **Build the data table** in a dedicated data table class;
2. **Render the data table** in a template, so the user can navigate through data;

Each of these steps is explained in detail in the next sections. To make examples easier to follow, all of them assume that you're building an application that displays a list of "products".

Users list projects using data table. Each project is an instance of the following `Product` class:

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

### Creating data tables in controllers

If your controller uses the [DataTableControllerTrait](src/DataTableControllerTrait.php), use the `createDataTableBuilder()` helper:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQuery;
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
You've also assigned each a [column type]() (e.g. `NumberType` and `TextType`), represented by its fully qualified class name.

> ### 💡 Important note
> Notice the use of the `ProxyQuery` class, which wraps the query builder.
> Classes implementing the `ProxyQueryInterface` are used to modify the underlying query by the data tables.
> Although only the [Doctrine ORM proxy query class]() is provided out-of-the-box, [creating custom proxy query classes]() is easy.

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
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQuery;
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

## Rendering data tables

Now that the data table has been created, the next step is to render it:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProductType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQuery;
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

Then, use some [data table helper functions](#using-twig-helper-functions) to render the data table contents:

```html
{# templates/product/index.html.twig #}
{{ data_table(data_table) }}
```

That's it! The [data_table() function]() renders all the columns.

> ### 💡 Important note
>
> The data table system is smart enough to access the value of the private `id` and `name` properties from each product returned by the query via the `getId()` and `getName()` methods on the `Product` class.
> Unless a property is public, it _must_ have a "getter" method so that [Symfony Property Accessor Component]() can read its value.
> For a boolean property, you can use an "isser" or "hasser" method (e.g. `isPublished()` or `hasReminder()`) instead of a getter (e.g. `getPublished` or `getReminder()`).

As short as this rendering is, it's not very flexible.
Usually, you'll need more control about how the entire data table or some of its parts look.
For example, thanks to the [Bootstrap 5 integration with data tables]() you can set this option to generate data tables compatible with the Bootstrap 5 CSS framework:

```yaml
# config/packages/kreyu_data_table.yaml
kreyu_data_table:
  themes: ['@KreyuDataTable/themes/bootstrap_5.html.twig']
```

In addition to the data table themes, this bundle allows you to customize the way any part of the table is rendered
with multiple functions to render each part separately (column values, headers, pagination, filtration form, etc.)

## Processing data tables

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
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQuery;
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
2. When the user submits the data table (e.g. changes current page), [handleRequest()]() recognizes this and immediately
   writes the submitted data into the data table. This works the same, as if you've manually extracted the submitted data
   and used the data table's `sort`, `paginate`, `filter` and `personalize` methods.

> ### 💡 Important note
> If you need more control over exactly when and how your data table is modified, you can use each feature dedicated method to handle the submissions:
>
> - [paginate()]() to handle pagination - with current page and limit of items per page;
> - [sort()]() to handle sorting - with fields and directions to sort the list. Supports sorting by multiple fields;
> - [filter()]() to handle filtration - with filters and their values and operators;
> - [personalize()]() to handle personalization - with columns visibility status and their order;
>
> The [handleRequest()]() method handles all of them manually.
> First argument of the method - the request object - is not tied to specific request implementation,
> although only the [HttpFoundation request handler]() is provided out-of-the-box, [creating custom data table request handler]() is easy.

## Other common data table features

### Passing options to data tables

If you [create data tables in classes](#creating-data-table-classes), when building the data table in the controller, you can pass custom options to it as the third optional argument of `createDataTable()`:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProductType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQuery;
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
That's because forms must declare all the options they accept using the `configureOptions()` method:

```php
// src/DataTable/Type/ProductType.php
namespace App\DataTable\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
// ...

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

## Filters

A data table can be filtered with a set of _filters_, each of which are built with the help of a filter _type_ (e.g. `StringType`, `EntityType`, etc),

### Available filter types

The following filter types are natively available in the bundle:

- Doctrine ORM
    - [StringType](docs/filter/types/doctrine/orm/string.md)
    - [NumericType](docs/filter/types/doctrine/orm/numeric.md)
    - [EntityType](docs/filter/types/doctrine/orm/entity.md)
    - [CallbackType](docs/filter/types/doctrine/orm/callback.md)
- Other
    - [FilterType](docs/filter/types/filter.md) 

### Creating custom filter type

See [How to Create a Custom Filter Type](docs/create_custom_filter_type.md).

### Creating filter type extension

See [How to Create a Filter Type Extension](docs/create_filter_type_extension.md).

### Filter operators

Because every filter can work differently, e.g. string filter can match exact string or just contain it, each filter supports a set of operators.

Supported operators are defined in the protected `getSupportedOperators()` method of the filter class.

By default, operator selector is not visible to the user. Because of that, first operator choice is always used. If you wish to override that, you can pass selector choices manually:

```php
public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
{
    $builder
        // StringFilter uses Operator::EQUAL by default
        ->addFilter('name', StringType::class, [
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
public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
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

### Filtration criteria persistence

By default, filtration criteria applied by the user is saved to the cache for later use.

This feature can be disabled by either:

a) setting the `filtration_persistence_enabled` option default value as `false` in the data table type class:

```php
// src/DataTable/Type/ProductType.php
namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('filtration_persistence_enabled', false);
    }
    
    // ...
}
```

b) passing the `filtration_persistence_enabled` option as `false` to the data table type class:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQuery;
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
        // ...

        // If using "createDataTable" method:
        $dataTable = $this->createDataTable(ProductType::class, new ProxyQuery($products), [
            'filtration_persistence_enabled' => false,
        ]);
	    
        // If using "createDataTableBuilder" method:
        $dataTableBuilder = $this->createDataTableBuilder($query, [
            'filtration_persistence_enabled' => false,	    
        ]);
	
        // ...
    }
}
```

#### Configuring the filtration persistence adapter

To read about the persistence adapters, see [persistence adapters](#persistence-adapters) section.

For filtration, by default, there's a cache adapter service already pre-configured:

```shell
bin/console debug:container kreyu_data_table.filtration.persistence.adapter.cache
```

_TODO: How to change filtration persistence adapter of the data table type._

#### Passing the persistence subject directly

In some cases, it may be more handy to provide a persistence subject directly, instead of using a provider.

_TODO: How to directly change filtration persistence subject of the data table type._
