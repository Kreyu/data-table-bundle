# HtmlExporterType

The [HtmlExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/PhpSpreadsheet/Exporter/Type/HtmlExporterType.php) represents an exporter that uses a [PhpSpreadsheet HTML writer](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/src/PhpSpreadsheet/Writer/Html.php).

!!! Note

    Please note that HTML file format has some limits regarding styling cells, number formatting, etc.  
    For more details, see [PhpSpreadsheet HTML Writer documentation](https://phpspreadsheet.readthedocs.io/en/latest/topics/reading-and-writing-to-file/#phpofficephpspreadsheetwriterhtml).

## Options

{% include-markdown "_html_options.md" heading-offset=2 %}

## Inherited options

{% include-markdown "_phpspreadsheet_options.md" heading-offset=2 %}
{% include-markdown "../_exporter_options.md" heading-offset=2 %}
