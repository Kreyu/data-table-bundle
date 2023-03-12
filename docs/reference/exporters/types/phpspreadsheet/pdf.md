# PdfExporterType

The [PdfExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/PhpSpreadsheet/Exporter/Type/PdfExporterType.php) represents an exporter that uses a [PhpSpreadsheet PDF writer](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/src/PhpSpreadsheet/Writer/Pdf.php).

!!! Note

    Please note that PDF file format has some limits regarding styling cells, number formatting, etc.  
    For more details, see [PhpSpreadsheet PDF Writer documentation](https://phpspreadsheet.readthedocs.io/en/latest/topics/reading-and-writing-to-file/#phpofficephpspreadsheetwriterpdf).

## Options

### `library`

**type**: `string` **allowed values**: `'dompdf'`, `'mpdf'` or `'tcpdf'`

PhpSpreadsheet’s PDF Writer is a wrapper for a 3rd-Party PDF Rendering library such as TCPDF, mPDF or Dompdf. 
You must now install a PDF rendering library yourself; but PhpSpreadsheet will work with a number of different libraries.

Currently, the following libraries are supported:

| Library  | Downloadable from                                                           | Option value |
|----------|-----------------------------------------------------------------------------|--------------|
| Dompdf   | [https://github.com/dompdf/dompdf](https://github.com/dompdf/dompdf)        | `dompdf`     |
| mPDF     | [https://github.com/mpdf/mpdf](https://github.com/mpdf/mpdf)                | `mpdf`       |
| TCPDF    | [https://github.com/tecnickcom/tcpdf](https://github.com/tecnickcom/tcpdf)  | `tcpdf`      |

The different libraries have different strengths and weaknesses. 
Some generate better formatted output than others, some are faster or use less memory than others, while some generate smaller .pdf files. 
It is the developers choice which one they wish to use, appropriate to their own circumstances.

### `orientation`

**type**: `string` **default**: `'default'` **allowed values**: `'default'`, `'landscape'` or `'portrait'`

PhpSpreadsheet will attempt to honor the orientation and paper size specified in the worksheet for each page it prints, 
if the renderer supports that. However, you can set all pages to have the same orientation and paper size, e.g.

```php
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\PdfExporterType;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

$builder
    ->addExporter('pdf', PdfExporterType::class, [
        'orientation' => PageSetup::ORIENTATION_LANDSCAPE,
    ])
;
```

## Inherited options

{% include-markdown "_html_options.md" heading-offset=2 %}
{% include-markdown "_phpspreadsheet_options.md" heading-offset=2 %}
{% include-markdown "../_exporter_options.md" heading-offset=2 %}
