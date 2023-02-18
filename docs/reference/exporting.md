# Exporting

A data table can be exported to various formats, using _exporters_, each of which are built 
with the help of an exporter _type_ (e.g. `CsvType`, `XlsxType`, etc).

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

## Downloading the file

To download an export file, use the `export()` method on the data table, which returns
an instance of HttpFoundation File object.

If you're using data tables in controllers, use it in combination with `isExporting()` method:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\DataTable\Type\ProductType;
use App\Repository\ProductRepository;
use Kreyu\Bundle\DataTableBundle\DataTableControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableControllerTrait;
    
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

## Built-in exporter types

The following exporter types are natively available in the bundle:

- PhpSpreadsheet
    - [CsvType](#csvtype)
    - [HtmlType](#htmltype)
    - [OdsType](#odstype)
    - [PdfType](#pdftype)
    - [XlsType](#xlstype)
    - [XlsxType](#xlsxtype)
    - [PhpSpreadsheetType](#phpspreadsheettype)
- Base types
    - [ExporterType](#exportertype)

{% include-markdown "exporters/creating_custom_exporter_type.md" heading-offset=1 %}

## Built-in types reference

{% include-markdown "exporters/types/phpspreadsheet/csv.md" heading-offset=2 %}
{% include-markdown "exporters/types/phpspreadsheet/html.md" heading-offset=2 %}
{% include-markdown "exporters/types/phpspreadsheet/ods.md" heading-offset=2 %}
{% include-markdown "exporters/types/phpspreadsheet/pdf.md" heading-offset=2 %}
{% include-markdown "exporters/types/phpspreadsheet/xls.md" heading-offset=2 %}
{% include-markdown "exporters/types/phpspreadsheet/xlsx.md" heading-offset=2 %}
{% include-markdown "exporters/types/phpspreadsheet/phpspreadsheet.md" heading-offset=2 %}
{% include-markdown "exporters/types/exporter.md" heading-offset=2 %}
