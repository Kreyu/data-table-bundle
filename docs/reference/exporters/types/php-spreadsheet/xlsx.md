---
label: XLSX
order: c
---

# XLSX exporter type

The `XlsxExporterType` represents an exporter that uses a [PhpSpreadsheet Xlsx writer](https://phpspreadsheet.readthedocs.io/en/latest/topics/reading-and-writing-to-file/#phpofficephpspreadsheetwriterxlsx).

+---------------------+--------------------------------------------------------------+
| Parent type         | [PhpSpreadsheetType](php-spreadsheet.md)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: XlsxExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/XlsxExporterType.php)
+---------------------+--------------------------------------------------------------+

## Options

### `office_2003_compatibility`

- **type**: `bool` 
- **default**: `false`

Because of a bug in the Office2003 compatibility pack, there can be some small issues when opening
Xlsx spreadsheets (mostly related to formula calculation). You can enable Office2003 compatibility by setting this option to `true`.

!!!warning Warning
Office2003 compatibility option should only be used when needed because it disables several Office2007 file format options,
resulting in a **lower-featured** Office2007 spreadsheet!
!!!

## Inherited options

{{ include '_php-spreadsheet_options.md' }}
