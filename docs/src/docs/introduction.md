# Introduction

DataTableBundle is a Symfony bundle that aims to help with data tables.

[[toc]]

<div class="tip custom-block" style="padding-top: 8px; margin-top: 30px;">

Just want to try it out? Skip to the [installation](installation.md).

</div>

## Features

- [Type classes](#similarity-to-form-component) for a class-based configuration, like in a Symfony Form component
- [Sorting](features/sorting.md), [filtering](features/filtering.md) and [pagination](features/pagination.md) - triforce of the data tables
- [Personalization](features/personalization.md) where the user decides the order and visibility of columns
- [Persistence](features/persistence.md) to save applied data (e.g. filters) between requests
- [Exporting](features/exporting.md) with or without applied pagination, filters and personalization 
- [Theming](features/theming.md) of every part of the bundle using Twig
- [Data source agnostic](features/extensibility.md) with optional Doctrine ORM integration bundle
- [Integration with Hotwire Turbo](features/asynchronicity.md) for asynchronicity

## Use cases

If your application uses an admin panel generator, like a SonataAdminBundle or EasyAdminBundle, you won't need this bundle.
Those generators already cover the definition of data tables in their own way.

However, if your application is complex enough that a simple panel generator is not enough, for example, a very specific B2B or CRM platform,
you may consider DataTableBundle, which focuses solely on the data tables, like a Form component focuses solely on the forms.
It can save a lot of time when compared to rendering tables manually (especially with filters), and helps with keeping visual homogeneity.

## Similarity to form component

Everything is designed to be friendly to a Symfony developers that used the Symfony Form component before.
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
            ->addFilter('id', NumberFilterType::class)
            ->addFilter('name', TextFilterType::class);
        
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
