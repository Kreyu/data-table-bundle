# Exporters

A data table can be exported to various formats, using _exporters_, each of which are built
with the help of an exporter _type_.

## Prerequisites

If you plan to use the built-in [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet) exporter types,
make sure you have the PhpSpreadsheet installed:

```bash
composer require phpoffice/phpspreadsheet
```

## Configuring the exporting feature

By default, the exporting is enabled for every data table type.

Every part of the exporting feature can be configured using the [data table options](#passing-options-to-data-tables):

- `exporting_enabled` - to enable/disable feature completely;

## Built-in exporter types

The following exporter types are natively available in the bundle:

- PhpSpreadsheet
    - [Csv](types/phpspreadsheet/csv.md)
    - [Html](types/phpspreadsheet/html.md)
    - [Ods](types/phpspreadsheet/ods.md)
    - [Pdf](types/phpspreadsheet/pdf.md)
    - [Xls](types/phpspreadsheet/xls.md)
    - [Xlsx](types/phpspreadsheet/xlsx.md)
    - [PhpSpreadsheet](types/phpspreadsheet/phpspreadsheet.md)
- Base types
    - [Exporter](types/exporter.md)

{% include-markdown "creating_custom_exporter_type.md" heading-offset=1 %}

## Downloading the file

To download an export file, use the `export()` method on the data table.

If you're using data tables in controllers, use it in combination with `isExporting()` method:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProductType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\DataTableAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableAwareTrait;
    
    public function index(Request $request, ProductRepository $repository): Response
    {
        // ...

        $dataTable = $this->createDataTable(ProductType::class, $query);
        $dataTable->handleRequest($request);
        
        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }
        
        // ...
    }
}
```