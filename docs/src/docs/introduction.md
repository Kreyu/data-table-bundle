# Introduction

DataTableBundle is a Symfony bundle that aims to help with data tables.

[[toc]]

<div class="tip custom-block" style="padding-top: 8px; margin-top: 30px;">

Just want to try it out? Skip to the [installation](installation.md).

</div>

## Features

- [Type classes](#similarity-to-form-component) for a class-based configuration, like in a Symfony Form component
- [Sorting](features/sorting.md), [filtering](features/filtering.md) and [pagination](features/pagination.md) - classic triforce of the data tables
- [Personalization](features/personalization.md) where the user decides the order and visibility of columns
- [Persistence](features/persistence.md) to save applied data (e.g. filters) between requests
- [Exporting](features/exporting.md) with or without applied pagination, filters and personalization 
- [Theming](features/theming.md) of every part of the bundle using Twig
- [Data source agnostic](features/extensibility.md) with Doctrine ORM supported out of the box
- [Asynchronicity](features/asynchronicity.md) thanks to integration with Hotwire Turbo

## Use cases

Imagine an application, that contains many listings - a list of products, categories, tags, clients, etc.
In most cases, we're returning a list of data to the view, and rendering it directly in the Twig.
Now, imagine, that the category details view should display a listing of its own products.
Some time later, the client requires a way to sort and filter the tables. 

This quickly becomes less and less maintainable as the system grows.
With this bundle, you could define a data table for each entity, with their columns, filters, actions and exporters.
Reusing the data tables (and its components) is as easy, as reusing the forms using the Symfony Form component.

However, if your application is using an admin panel generator, like a SonataAdminBundle or EasyAdminBundle, you definitely **don't** need this bundle.
Those generators already cover the definition of data tables in their own way.

Sometimes applications are complex enough, that a simple admin panel generator is not enough.
This is a case where this bundle shines - you can build a fully customized application, while delegating all the data table oriented work to the bundle.

## Similarity to form component

Everything is designed to be friendly to a Symfony developers that used the [Symfony Form component](https://github.com/symfony/form/) before.

::: tip Note
There are **many** similarities between those components - even in the source code!
Thanks to that, it should be easy to work with the bundle, and contribute as well.

Credits to all the creators and contributors of the [Symfony Form component](https://github.com/symfony/form/),
as they are the ones that came up with the idea of this type-based configuration, and this bundle only follows its principles.

Although, because Form component can be used outside a framework, and this bundle works only as a Symfony bundle,
the core is simplified as much as possible.
::: 

Data tables and their components - [columns](components/columns.md), [filters](components/filters.md), [actions](components/actions.md) and [exporters](components/exporters.md), are defined using type classes, like a forms:

```php
class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('id', NumberColumnType::class)
            ->addColumn('name', TextColumnType::class);
        
        $builder
            ->addFilter('id', NumericFilterType::class)
            ->addFilter('name', StringFilterType::class);
        
        $builder    
            ->addAction('create', ButtonActionType::class)
            ->addRowAction('update', ButtonActionType::class)
            ->addBatchAction('delete', ButtonActionType::class);
        
        $builder
            ->addExporter('csv', CsvExporterType::class)
            ->addExporter('xlsx', XlsxExporterType::class);
    }
}
```

Creating the data tables using those type classes may also seem very familiar:

```php
class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(Request $request): Response
    {
        $dataTable = $this->createDataTable(ProductDataTableType::class, $query);
        $dataTable->handleRequest($request);
        
        return $this->render('product/index.html.twig', [
            'products' => $dataTable->createView(),
        ])
    }
}
```

Rendering the data table in Twig is as simple as executing a single function:

```twig
{# templates/product/index.html.twig #}
<div class="card">
    {{ data_table(products, { title: 'Products' }) }}
</div>
```

## Recommended practices

When working with Form component, each "Type" refers to the form type.

When working with DataTableBundle, there are many new and different types - data table, column, filter, action and exporter types.

For readability, it is recommended to name form types with `FormType` suffix, instead of a simple `Type`. 
This makes a context of the type class clear:

- `ProductFormType` - defines product form; 
- `ProductDataTableType` - defines product list; 
- `ProductColumnType` - defines product column (if separate definition is needed);
- `ProductFilterType` - defines product filter (if separate definition is needed);
- etc.

Also, type extensions - instead of `TypeExtension`, use `FormTypeExtension` suffix.
