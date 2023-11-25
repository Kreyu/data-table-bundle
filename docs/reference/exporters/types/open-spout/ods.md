---
label: ODS
order: c
tags:
  - exporters
  - openspout
---

# OpenSpout ODS exporter type

The `OdsExporterType` represents an exporter that uses an [OpenSpout](https://github.com/openspout/openspout) ODS writer.

+---------------------+--------------------------------------------------------------+
| Parent type         | [OpenSpoutExporterType](open-spout.md)
+---------------------+--------------------------------------------------------------+
| Class               | [:icon-mark-github: OdsExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/OpenSpout/Exporter/Type/OdsExporterType.php)
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
