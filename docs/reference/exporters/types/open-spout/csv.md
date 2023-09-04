---
label: CSV
order: a
tags:
  - exporter
  - openspout
---

# OpenSpout CSV exporter type

[!badge Requires integration bundle installed](https://github.com/kreyu/data-table-open-spout-bundle)

The `CsvExporterType` represents an exporter that uses an [OpenSpout](https://github.com/openspout/openspout) CSV writer.

+---------------------+--------------------------------------------------------------+
| Parent type         | [OpenSpoutExporterType](open-spout.md)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: CsvExporterType](https://github.com/Kreyu/data-table-open-spout-bundle/blob/main/src/Exporter/Type/CsvExporterType.php)
+---------------------+--------------------------------------------------------------+

## Options

### `writer_options`

**type**: `callable` or `OpenSpout\Writer\CSV\Options`

Represents the writer options object used in the writer.
For more information and possible configuration, see [official documentation](https://github.com/openspout/openspout/blob/4.x/docs/documentation.md).

## Inherited options

{{ include '_open-spout_options.md' }}
{{ include '../_exporter_options.md' }}
