---
label: XLSX
order: b
tags:
  - exporters
  - openspout
---

# OpenSpout XLSX exporter type

The `XlsxExporterType` represents an exporter that uses an [OpenSpout](https://github.com/openspout/openspout) XLSX writer.

+---------------------+--------------------------------------------------------------+
| Parent type         | [OpenSpoutExporterType](open-spout.md)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: XlsxExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/OpenSpout/Exporter/Type/XlsxExporterType.php)
+---------------------+--------------------------------------------------------------+

## Options

### `default_row_style`

- **type**: `OpenSpout\Common\Entity\Style\Style`
- **default**: an unmodified instance of `Style` class

An instance of style class that will be applied to all rows.

### `should_create_new_sheets_automatically`

- **type**: `bool`
- **default**: `true`

Determines whether new sheets should be created automatically
when the maximum number of rows (1,048,576) per sheet is reached.

### `should_use_inline_strings`

- **type**: `bool`
- **default**: `true`

Determines whether inline strings should be used instead of shared strings.

For more information about this configuration, see [OpenSpout documentation](https://github.com/openspout/openspout/blob/4.x/docs/documentation.md#strings-storage-xlsx-writer).

### `default_column_width`

- **type**: `null` or `float`
- **default**: `null`

Represents a width that will be applied to all columns by default.

### `default_row_height`

- **type**: `null` or `float`
- **default**: `null`

Represents a height that will be applied to all rows by default.

## Inherited options

{{ include '_open-spout_options.md' }}
{{ include '../_exporter_options.md' }}
