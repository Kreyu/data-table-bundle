# PhpSpreadsheetExporterType

The [PhpSpreadsheetExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/PhpSpreadsheet/Exporter/Type/PhpSpreadsheetExporterType.php) represents a base exporter, 
used as a parent for every other PhpSpreadsheet-oriented type in the bundle.

## Options

### `pre_calculate_formulas`

**type**: `bool` **default**: `true`

By default, the PhpSpreadsheet writers pre-calculates all formulas in the spreadsheet. 
This can be slow on large spreadsheets, and maybe even unwanted. 
Value of this option determines whether the formula pre-calculation is enabled.

## Inherited options

{% include-markdown "_phpspreadsheet_options.md" heading-offset=2 %}
{% include-markdown "../_exporter_options.md" heading-offset=2 %}
