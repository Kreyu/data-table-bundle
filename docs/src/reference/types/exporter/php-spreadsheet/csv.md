<script setup>
    import ExporterTypeOptions from "../options/exporter.md";
    import PhpSpreadsheetExporterTypeOptions from "../options/php-spreadsheet.md";
</script>

# PhpSpreadsheet CsvExporterType

The [`CsvExporterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/PhpSpreadsheet/Exporter/Type/CsvExporterType.php) represents an exporter that uses an [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet) CSV writer.

## Options

### `delimiter`

**type**: `string` **default**: `','`

Represents a string that separates the CSV files values.

### `enclosure`

**type**: `string` **default**: `'"'`

Represents a string that wraps all CSV fields.

### `enclosure_required`

**type**: `bool` **default**: `true`

By default, all CSV fields are wrapped in the enclosure character.
Value of this option determines whether to use the enclosure character only when required.

### `line_ending`

**type**: `string` **default**: platform `PHP_EOL` constant value

Represents a string that separates the CSV files lines.

### `sheet_index`

**type**: `int` **default**: `0`

CSV files can only contain one worksheet. Therefore, you can specify which sheet to write to CSV.

### `use_bom`

**type**: `string` **default**: `false`

CSV files are written in UTF-8. If they do not contain characters outside the ASCII range, nothing else need be done.
However, if such characters are in the file, or if the file starts with the 2 characters 'ID', it should explicitly include a BOM file header;
if it doesn't, Excel will not interpret those characters correctly. This can be enabled by setting this option to `true`.

### `include_separator_line`

**type**: `bool` **default**: `false`

Determines whether a separator line should be included as the first line of the file.

### `excel_compatibility`

**type**: `bool` **default**: `false`

Determines whether the file should be saved with full Excel compatibility.

Note that this overrides other settings such as useBOM, enclosure and delimiter!

### `output_encoding`

**type**: `string` **default**: `''`

It can be set to output with the encoding that can be specified by PHP's `mb_convert_encoding` (e.g. `'SJIS-WIN'`).

### `decimal_separator`

**type**: `string` **default**: depends on the server's locale setting

If the worksheet you are exporting contains numbers with decimal separators,
then you should think about what characters you want to use for those before doing the export.

By default, PhpSpreadsheet looks up in the server's locale settings to decide what character to use.
But to avoid problems it is recommended to set the character explicitly.

### `thousands_separator`

**type**: `string` **default**: depends on the server's locale setting

If the worksheet you are exporting contains numbers with thousands separators,
then you should think about what characters you want to use for those before doing the export.

By default, PhpSpreadsheet looks up in the server's locale settings to decide what character to use.
But to avoid problems it is recommended to set the character explicitly.

## Inherited options

<ExporterTypeOptions />
<PhpSpreadsheetExporterTypeOptions />