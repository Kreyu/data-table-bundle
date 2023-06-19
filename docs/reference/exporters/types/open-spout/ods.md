---
label: ODS
order: c
---

# OpenSpout ODS exporter type

The `OdsExporterType` represents an exporter that uses an [OpenSpout](https://github.com/openspout/openspout) ODS writer.

+---------------------+--------------------------------------------------------------+
| Parent type         | [ExporterType](../exporter.md)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: OdsExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/OpenSpout/Exporter/Type/OdsExporterType.php)
+---------------------+--------------------------------------------------------------+

## Options

### `default_row_style`

**type**: `\OpenSpout\Common\Entity\Style\Style` **default**: object of class with default values

### `should_create_new_sheets_automatically`

**type**: `bool` **default**: `true`

### `default_column_width`

**type**: `null` or `int` **default**: `null`

### `default_row_height`

**type**: `null` or `int` **default**: `null`
