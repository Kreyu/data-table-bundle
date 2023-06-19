---
label: CSV
order: a
---

# OpenSpout CSV exporter type

The `CsvExporterType` represents an exporter that uses an [OpenSpout](https://github.com/openspout/openspout) CSV writer.

+---------------------+--------------------------------------------------------------+
| Parent type         | [ExporterType](../exporter.md)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: CsvExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/OpenSpout/Exporter/Type/CsvExporterType.php)
+---------------------+--------------------------------------------------------------+

## Options

### `field_delimiter`

**type**: `string` **default**: `','`

Represents a string that separates the CSV files values.

### `field_enclosure`

**type**: `string` **default**: `'"'`

Represents a string that wraps all CSV fields.

### `should_add_bom`

**type**: `bool` **default**: `true`

### `flush_threshold`

**type**: `int` **default**: `500`
