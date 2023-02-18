# PhpSpreadsheetType

The [PhpSpreadsheetType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/PhpSpreadsheetType.php) represents a base exporter, 
used as a parent for every other PhpSpreadsheet-oriented type in the bundle.

## Options

### `pre_calculate_formulas`

**type**: `bool` **default**: `true`

By default, the PhpSpreadsheet writers pre-calculates all formulas in the spreadsheet. 
This can be slow on large spreadsheets, and maybe even unwanted. 
Value of this option determines whether the formula pre-calculation is enabled.

## Inherited options

See [base exporter type documentation](/reference/exporting/#exportertype).
