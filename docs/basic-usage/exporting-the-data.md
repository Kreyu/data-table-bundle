---
order: g
---

# Exporting the data

Ability to export the data is crucial in some use cases. The exporters handle the exporting process.  
Similar to data tables and its columns and filters, exporters are defined using the [type classes](../features/type-classes.md).

![Export modal with the Tabler theme](./../static/export_modal.png)

## Prerequisites

The bundle comes with exporter types using the PhpSpreadsheet.
This library is not included as a bundle dependency, therefore, make sure it is installed:

:::flex
```bash
$ composer require phpoffice/phpspreadsheet
```
:::

## Adding exporters to the data table

To add exporter, use the builder's `addExporter()` method:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Columns and filters added before...
        
        $builder
            ->addExporter('csv', CsvExporterType::class)
            ->addExporter('xlsx', XlsxExporterType::class)
        ;
    }
}
```

The builder's `addExporter()` method accepts _three_ arguments:

- exporter name;
- exporter type — with a fully qualified class name;
- exporter options — defined by the exporter type, used to configure the exporter;

For reference, see [built-in exporter types](../reference/exporters/types.md).

## Downloading the file

To download an export file, use the `export()` method on the data table.

If you're using data tables in controllers, use it in combination with `isExporting()` method:

```php #15-17 src/Controller/ProductController.php
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
