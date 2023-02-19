# XlsxType

S|Requires PhpSpreadsheet||

The [XlsxType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/PhpSpreadsheet/Exporter/Type/XlsxType.php) represents an exporter that uses a [PhpSpreadsheet XLSX writer](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/src/PhpSpreadsheet/Writer/Xlsx.php).

## Options

### `office_2003_compatibility`

**type**: `bool` **default**: `false`

Because of a bug in the Office2003 compatibility pack, there can be some small issues when opening 
Xlsx spreadsheets (mostly related to formula calculation). You can enable Office2003 compatibility by setting this option to `true`.

Office2003 compatibility option should only be used when needed because it disables several Office2007 file format options, 
resulting in a lower-featured Office2007 spreadsheet!

## Inherited options

See [base PhpSpreadsheet exporter type documentation](/reference/exporting/#phpspreadsheettype).
