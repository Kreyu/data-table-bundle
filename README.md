<h1>
    <p align="center">
        <img width="75px" src="./docs/src/public/logo.png"/>
        <br>DataTableBundle
    </p>
</h1>

<p align="center">
    Streamlines the creation process of the data tables in Symfony applications.
    <br />
    <a href="#about">About</a>
    ·
    <a href="https://data-table-bundle.swroblewski.pl/">Documentation</a>
    ·
    <a href="https://data-table-bundle.swroblewski.pl/reference">Reference</a>
  </p>
</p>


## About

This bundle allows creating data tables in the same way as you probably do with forms, as
every component can be defined with a [type class] and reused across the application.

[type class]: https://data-table-bundle.swroblewski.pl/docs/introduction#similarity-to-form-component

Data tables can be [sorted], [filtered] and [paginated]. Users can [personalize] the order 
and visibility of columns. Those features can be [persisted] between requests, per user,
so closing the browser and coming back later will restore the previous state.

[sorted]: https://data-table-bundle.swroblewski.pl/docs/features/sorting
[filtered]: https://data-table-bundle.swroblewski.pl/docs/features/filtering
[paginated]: https://data-table-bundle.swroblewski.pl/docs/features/pagination
[personalize]: https://data-table-bundle.swroblewski.pl/docs/features/personalization
[persisted]: https://data-table-bundle.swroblewski.pl/docs/features/persistence

Works with Doctrine ORM and arrays out-of-the-box, but can be easily [integrated with any data source].
Supports [theming] with Twig and [exporting] to various data formats.

[integrated with any data source]: https://data-table-bundle.swroblewski.pl/docs/features/extensibility.html#proxy-queries
[theming]: https://data-table-bundle.swroblewski.pl/docs/features/theming
[exporting]: https://data-table-bundle.swroblewski.pl/docs/features/exporting

> [!WARNING]
> This bundle is still in development and is likely to **change**, or even **change drastically**.
> It is **NOT** production ready, and backwards compatibility is **NOT** guaranteed until the first stable release. 


## Familiarity

If you've ever worked with forms in Symfony, you should feel right at home:

```php
class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('id', NumberColumnType::class)
            ->addColumn('name', TextColumnType::class)
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'product');
    }
}
```

Creating the data tables using those type classes may also seem very familiar:

```php
class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        $queryBuilder = $repository->createDataTableQueryBuilder();
        
        $dataTable = $this->createDataTable(ProductDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request);
        
        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }
        
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
