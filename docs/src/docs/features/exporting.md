# Exporting

The data tables can be _exported_, with use of the [exporters](#).

::: details Screenshots
![Export modal with the Tabler theme](/export_modal.png)
:::

[[toc]]

## Toggling the feature

By default, the exporting feature is **enabled** for every data table.
This can be configured with the `exporting_enabled` option:

::: code-group
```yaml [Globally (YAML)]
kreyu_data_table:
  defaults:
    exporting:
      enabled: true
```

```php [Globally (PHP)]
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->exporting()->enabled(true);
};
```

```php [For data table type]
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDataTableType extends AbstractDataTableType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'exporting_enabled' => true,
        ]);
    }
}
```

```php [For specific data table]
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
                'exporting_enabled' => true,
            ],
        );
    }
}
```
:::

::: tip Enabling the feature does not mean that any column will be exportable by itself.
By default, columns **are not** exportable.
:::

## Making the columns exportable

To make any column exportable, use its `export` option:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('id', NumberColumnType::class, [
                'export' => true,
            ])
        ;
    }
}
```

The column can be configured separately for the export by providing the array in the `export` option.
For example, to change the label of the column in the export:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('category', TextColumnType::class, [
                'export' => [
                    'label' => 'Category Name',
                ],
            ])
        ;
    }
}
```

## Default export configuration

The default export data, such as filename, exporter, strategy and a flag whether the personalization should be included,
can be configured using the data table builder's `setDefaultExportData()` method:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->setDefaultExportData(ExportData::fromArray([
                'filename' => sprintf('products_%s', date('Y-m-d')),
                'exporter' => 'xlsx',
                'strategy' => ExportStrategy::IncludeAll,
                'include_personalization' => true,
            ]))
        ;
    }
}
```

## Handling the export form

In the controller, use the `isExporting()` method to make sure the request should be handled as an export:

```php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function index(Request $request)
    {
        $dataTable = $this->createDataTable(ProductDataTableType::class);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }
    }
}
```

## Exporting without user input

To export the data table manually, without user input, use the `export()` method directly:

```php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function index()
    {
        $dataTable = $this->createDataTable(ProductDataTableType::class);

        // An instance of ExportFile, which extends the HttpFoundation File object
        $file = $dataTable->export();
        
        // For example, save it manually:
        $file->move(__DIR__);
        
        // Or return a BinaryFileResponse to download it in browser:   
        return $this->file($file);
    }
}
```

The export data (configuration, e.g. a filename) can be included by passing it directly to the `export()` method:

```php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function index()
    {
        $dataTable = $this->createDataTable(ProductDataTableType::class);

        $exportData = ExportData::fromDataTable($dataTable);
        $exportData->filename = sprintf('products_%s', date('Y-m-d'));
        $exportData->includePersonalization = false;
        
        $file = $dataTable->export($exportData);
        
        // ...
    }
}
```

## Optimization with Doctrine ORM

The exporting process including all pages of the large datasets can take a very long time.
To optimize this process, when using Doctrine ORM, change the hydration mode to array during the export:

```php
use Doctrine\ORM\AbstractQuery;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvent;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(DataTableEvents::PRE_EXPORT, function (DataTableEvent $event) {
            $event->getDataTable()->getQuery()->setHydrationMode(AbstractQuery::HYDRATE_ARRAY);
        });
    }
}
```

This will prevent the Doctrine ORM from hydrating the entities, which is not needed for the export.
Unfortunately, this means each exportable column property path has to be changed to array (wrapped in square brackets):

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('id', NumberColumnType::class, [
                'export' => [
                    'property_path' => '[id]',
                ],
            ])
        ;
    }
}

```

## Events

The following events are dispatched when `export()` method of the [`DataTableInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/DataTableInterface.php) is called:

::: info PRE_EXPORT
Dispatched before the exporter is called.
Can be used to modify the exporting data (configuration), e.g. to force an export strategy or change the filename.

**See**: [`DataTableEvents::PRE_EXPORT`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTableEvents.php)
:::

The dispatched events are instance of the [`DataTablePersonalizationEvent`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Event/DataTablePersonalizationEvent.php):

```php
use Kreyu\Bundle\DataTableBundle\Event\DataTableExportEvent;

class DataTableExportListener
{
    public function __invoke(DataTableExportEvent $event): void
    {
        $dataTable = $event->getDataTable();
        $exportData = $event->getExportData();
        
        // for example, modify the export data (configuration), then save it in the event
        $event->setExportData($exportData); 
    }
}
```