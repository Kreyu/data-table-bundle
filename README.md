# DataTableBundle

<img align="right" width="200px" src="./docs/src/public/logo.png"/>

[![Latest Stable Version](http://poser.pugx.org/kreyu/data-table-bundle/v)](https://packagist.org/packages/kreyu/data-table-bundle)

<div style="float: right">
![Logo](http://localhost:5173/logo.png)
</div>

![Logo](./docs/src/public/logo.png)

Streamlines the creation process of the data tables in Symfony applications.

> [!WARNING]
> This bundle is still in early stages of development and is likely to **change**, or even **change drastically**.
> It is **NOT** production ready, and backwards compatibility is **NOT** guaranteed until the first stable release. 

## Documentation

Check out the [official documentation](https://data-table-bundle.swroblewski.pl).

## Features

- [Type classes](https://data-table-bundle.swroblewski.pl/docs/introduction#similarity-to-form-component) for a class-based configuration, like in a Symfony Form component
- [Sorting](https://data-table-bundle.swroblewski.pl/docs/features/sorting), [filtering](https://data-table-bundle.swroblewski.pl/docs/features/filtering) and [pagination](https://data-table-bundle.swroblewski.pl/docs/features/pagination) - classic triforce of the data tables
- [Personalization](https://data-table-bundle.swroblewski.pl/docs/features/features/personalization) where the user decides the order and visibility of columns
- [Persistence](https://data-table-bundle.swroblewski.pl/docs/features/persistence) to save applied data (e.g. filters) between requests
- [Exporting](https://data-table-bundle.swroblewski.pl/docs/features/exporting) with or without applied pagination, filters and personalization
- [Theming](https://data-table-bundle.swroblewski.pl/docs/features/theming) of every part of the bundle using Twig
- [Data source agnostic](https://data-table-bundle.swroblewski.pl/docs/features/extensibility) with Doctrine ORM supported out of the box
- [Asynchronicity](https://data-table-bundle.swroblewski.pl/docs/features/asynchronicity) thanks to integration with Hotwire Turbo

## Use cases

Imagine an application, that contains many listings - a list of products, categories, tags, clients, etc.
In most cases, we're returning a list of data to the view, and rendering it directly in the Twig.
Now, imagine, that the category details view should display a listing of its own products.
Some time later, the client requires a way to sort and filter the tables.

This quickly becomes less and less maintainable as the system grows.
With this bundle, you could define a data table for each entity, with their columns, filters, actions and exporters, each using a simple PHP class.
Reusing the data tables (and its components) is as easy, as reusing the forms using the Symfony Form component.

However, if your application is using an admin panel generator, like a SonataAdminBundle or EasyAdminBundle, you definitely **don't** need this bundle.
Those generators already cover the definition of data tables in their own way.

Sometimes applications are complex enough, that an admin generator would be either too simple, or too limiting.
This is a case where this bundle shines - you can build a fully customized application, while delegating all the data table oriented work to the bundle.

## Similarity to form component

Everything is designed to be friendly to a Symfony developers that used the [Symfony Form component](https://github.com/symfony/form/) before.

> [!NOTE]
> There are **many** similarities between those components - even in the source code!
> Thanks to that, it should be easy to work with the bundle, and contribute as well.
>
> Credits to all the creators and contributors of the [Symfony Form component](https://github.com/symfony/form/),
> as they are the ones that came up with the idea of this type-based configuration, and this bundle only follows its principles.
>
> Although, because Form component can be used outside a framework, and this bundle works only as a Symfony bundle,
> the core is simplified as much as possible.

Data tables and their components - columns, filters, actions and exporters, are defined using type classes, like a forms:

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

## License

The MIT License (MIT). Please see [license file](LICENSE) for more information.
