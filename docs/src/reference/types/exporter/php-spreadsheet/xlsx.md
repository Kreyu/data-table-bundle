<script setup>
    import ExporterTypeOptions from "../options/exporter.md";
    import PhpSpreadsheetExporterTypeOptions from "../options/php-spreadsheet.md";
</script>

# PhpSpreadsheet XlsxExporterType

The [`XlsxExporterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/PhpSpreadsheet/Exporter/Type/XlsxExporterType.php) represents an exporter that uses an [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet) XLSX writer.

## Options

### `office_2003_compatibility`

- **type**: `bool`
- **default**: `false`

Because of a bug in the Office2003 compatibility pack, there can be some small issues when opening
Xlsx spreadsheets (mostly related to formula calculation). You can enable Office2003 compatibility by setting this option to `true`.

## Inherited options

<ExporterTypeOptions />
<PhpSpreadsheetExporterTypeOptions />
