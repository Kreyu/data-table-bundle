---
label: CSV
order: a
tags:
  - exporters
  - openspout
---

# OpenSpout CSV exporter type

The `CsvExporterType` represents an exporter that uses an [OpenSpout](https://github.com/openspout/openspout) CSV writer.

+---------------------+--------------------------------------------------------------+
| Parent type         | [OpenSpoutExporterType](open-spout.md)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: CsvExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/OpenSpout/Exporter/Type/CsvExporterType.php)
+---------------------+--------------------------------------------------------------+

## Options

### `field_delimiter`

- **type**: `string` 
- **default**: `','`

Represents a string that separates the values.

### `field_enclosure`

- **type**: `string` 
- **default**: `'"'`

Represents a string that wraps the values.

### `should_add_bom`

- **type**: `bool` 
- **default**: `true`

Determines whether a BOM character should be added at the beginning of the file. 

### `flush_threshold`

- **type**: `int` 
- **default**: `500`

Represents a number of rows after which the output should be flushed to a file.

## Inherited options

{{ include '_open-spout_options.md' }}
{{ include '../_exporter_options.md' }}
