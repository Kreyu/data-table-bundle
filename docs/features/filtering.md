---
order: c
---

# Filtering

The data tables can be _filtered_, with use of the [filters](../reference/filters/types.md).

## Toggling the feature

By default, the filtration feature is **enabled** for every data table.
This can be configured with the `filtration_enabled` option:

+++ Globally (YAML)
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    filtration:
      enabled: true
```
+++ Globally (PHP)
```php # config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->filtration()->enabled(true);
};
```
+++ For data table type
```php # src/DataTable/Type/ProductDataTable.php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'filtration_enabled' => true,
        ]);
    }
}
```
+++ For specific data table
```php # src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index()
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $query,
            options: [
                'filtration_enabled' => true,
            ],
        );
    }
}
```
+++

## Configuring the feature persistence

By default, the filtration feature [persistence](persistence.md) is **disabled** for every data table.

You can configure the [persistence](persistence.md) globally using the package configuration file, or its related options:

+++ Globally (YAML)
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    filtration:
      persistence_enabled: true
      # if persistence is enabled and symfony/cache is installed, null otherwise
      persistence_adapter: kreyu_data_table.filtration.persistence.adapter.cache
      # if persistence is enabled and symfony/security-bundle is installed, null otherwise
      persistence_subject_provider: kreyu_data_table.persistence.subject_provider.token_storage
```
+++ Globally (PHP)
```php # config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->filtration()
        ->persistenceEnabled(true)
        // if persistence is enabled and symfony/cache is installed, null otherwise
        ->persistenceAdapter('kreyu_data_table.filtration.persistence.adapter.cache')
        // if persistence is enabled and symfony/security-bundle is installed, null otherwise
        ->persistenceSubjectProvider('kreyu_data_table.persistence.subject_provider.token_storage')
    ;
};
```
+++ For data table type
```php # src/DataTable/Type/ProductDataTable.php
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function __construct(
        #[Autowire(service: 'kreyu_data_table.filtration.persistence.adapter.cache')]
        private PersistenceAdapterInterface $persistenceAdapter,
        #[Autowire(service: 'kreyu_data_table.persistence.subject_provider.token_storage')]
        private PersistenceSubjectProviderInterface $persistenceSubjectProvider,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'filtration_persistence_enabled' => true,
            'filtration_persistence_adapter' => $this->persistenceAdapter,
            'filtration_persistence_subject_provider' => $this->persistenceSubjectProvider,
        ]);
    }
}
```
+++ For specific data table
```php # src/Controller/ProductController.php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function __construct(
        #[Autowire(service: 'kreyu_data_table.filtration.persistence.adapter.cache')]
        private PersistenceAdapterInterface $persistenceAdapter,
        #[Autowire(service: 'kreyu_data_table.persistence.subject_provider.token_storage')]
        private PersistenceSubjectProviderInterface $persistenceSubjectProvider,
    ) {
    }
    
    public function index()
    {
        $dataTable = $this->createDataTable(
            type: ProductDataTableType::class, 
            query: $query,
            options: [
                'filtration_persistence_enabled' => true,
                'filtration_persistence_adapter' => $this->persistenceAdapter,
                'filtration_persistence_subject_provider' => $this->persistenceSubjectProvider,
            ],
        );
    }
}
```
+++

## Adding the filters

To add a filter, use the `addFilter()` method on the data table builder:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('id', NumericFilterType::class)
            ->addFilter('name', StringFilterType::class)
        ;
    }
}
```

The builder's `addFilter()` method accepts _three_ arguments:

- filter name — which in most cases will represent a property path in the underlying entity;
- filter type — with a fully qualified class name;
- filter options — defined by the filter type, used to configure the filter;

For reference, see [built-in filter types](../reference/filters/types.md).

### Specifying the query path

The bundle will use the filter name as the path to perform filtration on.
However, if the path is different from the column name, provide it using the `query_path` option:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('category', StringFilterType::class, [
                'query_path' => 'category.name',
            ])
        ;
    }
}
```

For reference, see [built-in filter types](../reference/filters/types.md).

## Filter operators

Each filter can support multiple operators, such as "equals", "contains", "starts with", etc. 
Optionally, the filtration form can display the operator selector, letting the user select a desired filtration method.

### **Default operator**

The default operator can be configured using the `default_operator` option:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('id', NumericFilterType::class)
            ->addFilter('name', StringFilterType::class, [
                'default_operator' => Operator::Contains,
            ])
        ;
    }
}
```

If the operator **is** selectable by the user, the `default_operator` determines the initially selected operator.

If the operator **is not** selectable by the user, the operator provided by this option will be used.

### Displaying operator selector

The operator can be selectable by the user by setting the `operator_selectable` option to `true`:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('id', NumericFilterType::class)
            ->addFilter('name', StringFilterType::class, [
                'operator_selectable' => true,
            ])
        ;
    }
}
```

Setting the `operator_selectable` to `false` (by default) changes the operator form type to `HiddenType`.
Because of that, even if you provide a different type using the `operator_form_type` option, it will be ignored.

### Restricting selectable operators

The operators selectable by the user can be restricted by using the `supported_operators` option:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('id', NumericFilterType::class)
            ->addFilter('name', StringFilterType::class, [
                'operator_selectable' => true,
                'supported_operators' => [
                    Operator::Equals,
                    Operator::Contains,
                ],
            ])
        ;
    }
}
```

Remember that each filter can support a different set of operators internally!

## Configuring form type

The filter form type can be configured using the `form_type` and `form_options` options.

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('id', NumericFilterType::class)
            ->addFilter('name', StringFilterType::class, [
                'form_type' => SearchType::class,
                'form_options' => [
                    'attr' => [
                        'placeholder' => 'Name', 
                    ],
                ],
            ])
        ;
    }
}
```

Similar configuration can be applied to the operator form type, using the `operator_form_type` and `operator_form_options` options:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addFilter('id', NumericFilterType::class)
            ->addFilter('name', StringFilterType::class, [
                'operator_form_type' => ChoiceType::class,
                'operator_form_options' => [
                    'attr' => [
                        'placeholder' => 'Operator', 
                    ],
                ],
            ])
        ;
    }
}
```

Setting the `operator_selectable` to `false` (by default) changes the operator form type to `HiddenType`.
Because of that, even if you provide a different type using the `operator_form_type` option, it will be ignored.

## Configuring default filtration

The default filtration data can be overridden using the data table builder's `setDefaultFiltrationData()` method:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->setDefaultFiltrationData(new FiltrationData([
            'id' => new FilterData(value: 1, operator: Operator::Contains),
        ]));
        
        // or by creating the filtration data from an array:
        $builder->setDefaultFiltrationData(FiltrationData::fromArray([
            'id' => ['value' => 1, 'operator' => 'contains'],
        ]));
    }
}
```
