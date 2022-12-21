# DataTableBundle

Streamlines creation process of the data tables.

## Usage

First, create data table type class, and define its columns and filters:

```php
// src/DataTable/Type/ProductType.php

class ProductType extends AbstractType
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function configureColumns(ColumnMapperInterface $columns): void
    {
        $columns
            ->add('name', TextType::class, [
                'label' => t('Nazwa'),
                'sort_field' => 'product.name',
            ])
            ->add('quantity', NumberType::class, [
                'label' => t('Ilość'),
            ])
            ->add('category', LinkType::class, [
                'label' => t('Kategoria'),
                'url' => function (Category $category): string {
                    return $this->urlGenerator->generate('app_category_show', [
                        'id' => $category->getId(),
                    ]);
                },
                'value' => function (Category $category): string {
                    return $category->getName();
                },
            ])
            ->add('actions', ActionsType::class, [
                'label' => t('Akcje'),
                'actions' => [
                    'show' => [
                        'template_path' => '@KreyuDataTable\Action\action_link_button.html.twig',
                        'template_vars' => [
                            'label' => t('Szczegóły'),
                            'url' => fn (Product $product) => $this->urlGenerator->generate('app_product_show', [
                                'id' => $product->getId(),
                            ]),
                        ],
                    ],
                ],
            ])
        ;
    }

    public function configureFilters(FilterMapperInterface $filters): void
    {
        $filters
            ->add('name', StringFilter::class, [
                'label' => t('Nazwa'),
                'field_name' => 'product.name',
                'operator_options' => [
                    'choices' => [
                        Operator::CONTAINS,
                    ],
                ],
            ])
            ->add('quantity', NumericFilter::class, [
                'label' => t('Ilość'),
                'field_name' => 'product.quantity',
                'operator_options' => [
                    'visible' => true,
                ],
            ])
            ->add('category', EntityFilter::class, [
                'label' => t('Kategoria'),
                'field_options' => [
                    'class' => Category::class,
                    'choice_label' => 'name',
                ],
            ])
        ;
    }
}
```

then, use it in a controller:

```php
// src/Controller/ProductController.php

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
    public function index(Request $request, ProductRepository $productRepository)
    {
        $query = $productRepository->createQueryBuilder('product');
        
        $dataTable = $this->createDataTable(ProductType::class, $query);
        $dataTable->handleRequest($request);
        
        return $this->render('product/index.html.twig', [
            'data_table' => $dataTable->createView(),
        ]);
    }
}
```

and render it inside the template:

```html
{% extends 'base.html.twig' %}

{% block content %}
    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{ render_data_table(data_table) }}
                </div>
            </div>
        </div>
    </div>
{% endblock %}
```