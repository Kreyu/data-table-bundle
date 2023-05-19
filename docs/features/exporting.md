---
order: e
---

# Exporting

The data tables can be _exported_, with use of the [exporters](../reference/exporters/types.md).

![Export modal with the Tabler theme](./../static/export_modal.png)

## Prerequisites

The built-in exporter types require [PhpSpreadsheet](https://phpspreadsheet.readthedocs.io/en/latest/).
This library is not included as a bundle dependency, therefore, you have to make sure it is installed:

```bash
$ composer require phpoffice/phpspreadsheet
```

## Toggling the feature

By default, the exporting feature is **enabled** for every data table.

You can change this setting globally using the package configuration file, or use `exporting_enabled` option:

+++ Globally (YAML)
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    exporting:
      enabled: true
```
+++ Globally (PHP)
```php # config/packages/kreyu_data_table.php
use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();
    $defaults->exporting()->enabled(true);
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
            'exporting_enabled' => true,
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
                'exporting_enabled' => true,
            ],
        );
    }
}
```
+++

## Adding the exporters

To add exporter, use the builder's `addExporter()` method on the data table builder:

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\CsvExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\XlsxExporterType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
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

## Adding multiple exporters of the same type

Let's think of a scenario where the user wants to export the data table to CSV format,
but there's a catch — it must be possible to export as either comma or semicolon separated file.

```php # src/DataTable/Type/ProductDataTableType.php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addExporter('csv_comma', CsvExporterType::class, [
                'label' => 'CSV (separated by comma)',
                'delimiter' => ',',
            ])
            ->addExporter('csv_semicolon', CsvExporterType::class, [
                'label' => 'CSV (separated by semicolon)',
                'delimiter' => ';',
            ])
            ->addExporter('xlsx', XlsxExporterType::class)
        ;
    }
}
```

## Downloading the file

To download an export file, use the `export()` method on the data table.

If you're using data tables in controllers, use it in combination with `isExporting()` method:

```php #17-19 src/Controller/ProductController.php
namespace App\Controller;

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

