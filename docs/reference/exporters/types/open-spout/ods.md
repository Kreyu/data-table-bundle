---
label: ODS
order: c
tags:
  - exporter
  - openspout
---

# OpenSpout ODS exporter type

The `OdsExporterType` represents an exporter that uses an [OpenSpout](https://github.com/openspout/openspout) ODS writer.

+---------------------+--------------------------------------------------------------+
| Parent type         | [OpenSpoutExporterType](open-spout.md)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: OdsExporterType](https://github.com/Kreyu/data-table-open-spout-bundle/blob/main/src/Bridge/OpenSpout/Exporter/Type/OdsExporterType.php)
+---------------------+--------------------------------------------------------------+

## Options

### `writer_options`

**type**: `callable` or `OpenSpout\Writer\ODS\Options`

Represents the writer options object used in the writer.
For more information and possible configuration, see [official documentation](https://github.com/openspout/openspout/blob/4.x/docs/documentation.md).

## Inherited options

{{ include '_open-spout_options.md' }}
{{ include '../_exporter_options.md' }}
